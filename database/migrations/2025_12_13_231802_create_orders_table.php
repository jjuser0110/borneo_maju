<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('order_no')->nullable();
            $table->date('order_datetime')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('account_no')->nullable();
            $table->string('fullname')->nullable();
            $table->double('idr_rate')->nullable();
            $table->double('myr_amount')->nullable();
            $table->double('idr_amount')->nullable();
            $table->double('processing_fees')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('status')->default('pending');
            $table->datetime('status_at')->nullable();
            $table->integer('status_by_id')->nullable();
            $table->double('duration')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
