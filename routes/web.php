<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Public sign-up and email password reset are not used: accounts are created by admins/agents.
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/change_password', [App\Http\Controllers\HomeController::class, 'change_password'])->name('change_password');
Route::get('/my_point_logs', [App\Http\Controllers\HomeController::class, 'my_point_logs'])->name('my_point_logs');
Route::post('/removeimage/{image_id}', 'HomeController@removeimage')->middleware(['auth', 'role:1,2'])->name('removeimage');
Route::get('/get_pending', 'HomeController@get_pending')->middleware(['auth', 'role:1,2'])->name('get_pending');
Route::post('/change_language', [App\Http\Controllers\HomeController::class, 'change_language'])->name('change_language');
