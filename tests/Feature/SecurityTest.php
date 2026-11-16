<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Attack checks for access control, points, orders and uploads. Runs inside a DB transaction that is rolled back. */
class SecurityTest extends TestCase
{
    use DatabaseTransactions;

    private function mk(int $role, array $extra = []): User
    {
        return User::create(array_merge([
            'name' => 'tmp', 'username' => 'zz' . uniqid(), 'password' => Hash::make('secret123'),
            'role_id' => $role, 'is_active' => 1, 'idr_rate' => 3500, 'processing_fees' => 1,
            'point' => 100, 'limit' => 5000, 'idr_limit' => 100,
        ], $extra));
    }

    public function test_attacks_are_blocked_and_normal_pages_work(): void
    {
        $this->withoutVite();
        $admin = $this->mk(1);
        $staff = $this->mk(2);
        $agent = $this->mk(3, ['upline' => $admin->id, 'point' => 100]);
        $down  = $this->mk(3, ['upline' => $agent->id, 'point' => 50]);
        $other = $this->mk(3, ['upline' => $admin->id, 'point' => 500]);

        // --- Agent can't reach admin functions
        $this->actingAs($agent)->get('/admin/index')->assertForbidden();
        $this->actingAs($agent)->post('/admin/store', ['username' => 'hacker', 'password' => 'secret123'])->assertForbidden();
        $this->actingAs($agent)->post('/bank_setting/adjust_money', ['bank_setting_id' => 1, 'amount' => 1])->assertForbidden();
        $this->actingAs($staff)->get('/agent/index')->assertForbidden();
        $this->actingAs($staff)->get('/order/index')->assertForbidden();
        $this->actingAs($agent)->get('/order/pending')->assertForbidden();
        $this->assertDatabaseMissing('users', ['username' => 'hacker']);

        // --- Agent can't promote/top-up itself
        $this->actingAs($agent)->post("/agent/update/{$agent->id}", ['role_id' => 1, 'point' => 9999999,
            'idr_rate' => 3500, 'processing_fees' => 1, 'limit' => 1, 'idr_limit' => 1])->assertForbidden();

        // --- Editing own downline ignores role/point/upline
        $this->actingAs($agent)->post("/agent/update/{$down->id}", ['role_id' => 1, 'point' => 9999999, 'upline' => $other->id,
            'idr_rate' => 3500, 'processing_fees' => 1, 'limit' => 100, 'idr_limit' => 10])->assertRedirect();
        $down->refresh();
        $this->assertEquals(3, $down->role_id);
        $this->assertEquals(50, $down->point);
        $this->assertEquals($agent->id, $down->upline);

        // --- Agent can't steal points from someone else's agent
        $this->actingAs($agent)->post("/agent/deduct-point/{$other->id}", ['point' => 400])->assertForbidden();
        $this->assertEquals(500, $other->fresh()->point);
        $this->actingAs($agent)->get("/agent/viewlog/{$other->id}")->assertForbidden();
        $this->actingAs($agent)->get("/report/sales_report?user_id={$other->id}")->assertForbidden();

        // --- Negative / oversize starting points rejected
        $this->actingAs($agent)->post('/agent/store', ['name' => 'n', 'username' => 'neg', 'password' => 'secret123', 'point' => -100000,
            'idr_rate' => 3500, 'processing_fees' => 1, 'limit' => 1, 'idr_limit' => 1])->assertSessionHasErrors();
        $this->actingAs($agent)->post('/agent/store', ['name' => 'n', 'username' => 'big', 'password' => 'secret123', 'point' => 1000,
            'idr_rate' => 3500, 'processing_fees' => 1, 'limit' => 1, 'idr_limit' => 1])->assertSessionHasErrors();
        $this->assertEquals(100, $agent->fresh()->point);
        // ...but a legit transfer works
        $this->actingAs($agent)->post('/agent/store', ['name' => 'n', 'username' => 'ok', 'password' => 'secret123', 'point' => 30,
            'idr_rate' => 3500, 'processing_fees' => 1, 'limit' => 1, 'idr_limit' => 1])->assertSessionHasNoErrors();
        $this->assertEquals(70, $agent->fresh()->point);
        $this->assertDatabaseHas('users', ['username' => $agent->username . 'ok', 'role_id' => 3, 'upline' => $agent->id, 'point' => 30]);

        // --- Orders: can't fake idr_amount or status
        $base = ['order_no' => $agent->username . '2611' . '0001', 'myr_amount' => 10, 'idr_rate' => 3500,
            'processing_fees' => 1, 'bank_id' => null, 'account_no' => '1', 'fullname' => 'x'];
        $this->actingAs($agent)->post('/order/store', $base + ['idr_amount' => 99999999])->assertSessionHasErrors();
        $this->actingAs($agent)->post('/order/store', array_merge($base, ['order_no' => $other->username . '1'], ['idr_amount' => 35000]))->assertSessionHasErrors();
        $this->actingAs($agent)->post('/order/store', $base + ['idr_amount' => 35000, 'status' => 'completed'])->assertSessionHasNoErrors();
        $order = Order::where('order_no', $base['order_no'])->firstOrFail();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($agent->id, $order->user_id);

        // --- Other agents can't see / process it
        $this->actingAs($other)->get("/order/view_details/{$order->id}")->assertForbidden();
        $this->actingAs($other)->post("/order/pending_update/{$order->id}", ['status' => 'cancelled'])->assertForbidden();
        $this->actingAs($other)->post("/order/update/{$order->id}", $base + ['idr_amount' => 35000])->assertRedirect(route('order.index'));
        $this->actingAs($agent)->get("/order/view_details/{$order->id}")->assertOk();

        // --- PHP upload rejected
        $php = UploadedFile::fake()->createWithContent('shell.php', '<?php system($_GET["c"]);');
        $this->actingAs($staff)->post("/order/pending_update/{$order->id}", ['remarks' => 'x', 'receipt' => $php])->assertSessionHasErrors('receipt');
        $this->assertDatabaseMissing('file_attachments', ['file_type' => 'php']);

        // --- Double cancel only refunds once
        $before = $agent->fresh()->point;
        $this->actingAs($staff)->post("/order/pending_update/{$order->id}", ['status' => 'cancelled']);
        $this->actingAs($staff)->post("/order/pending_update/{$order->id}", ['status' => 'cancelled']);
        $this->assertEquals($before + 10, $agent->fresh()->point);

        // --- Registration gone, GET delete gone
        $this->get('/register')->assertNotFound();
        $this->actingAs($admin)->get("/staff/destroy/{$staff->id}")->assertStatus(405);

        // --- Locked account is logged out
        $locked = $this->mk(3, ['upline' => $admin->id, 'is_active' => 0]);
        $this->actingAs($locked)->get('/order/index')->assertRedirect(route('login'));

        // --- Normal pages still load per role
        foreach (['/home', '/admin/index', '/staff/index', '/agent/index', '/order/index', '/order/pending', '/bank/index',
                  '/bank_setting/index', '/cost/index', '/report/daily_report', '/report/profit_list', '/report/sales_report',
                  '/order/create', "/agent/edit/{$agent->id}", "/agent/viewlog/{$agent->id}", "/order/view/{$order->id}"] as $u) {
            $this->actingAs($admin)->get($u)->assertOk();
        }
        foreach (['/order/pending', "/order/view_details/{$order->id}", '/get_pending', '/my_point_logs'] as $u) {
            $this->actingAs($staff)->get($u)->assertOk();
        }
        foreach (['/order/index', '/order/create', '/agent/index', '/agent/create', '/report/sales_report', '/my_point_logs',
                  "/agent/edit/{$down->id}", "/agent/viewlog/{$down->id}", "/agent/downline/{$down->id}",
                  "/report/sales_report?user_id={$down->id}"] as $u) {
            $this->actingAs($agent)->get($u)->assertOk();
        }
    }
}
