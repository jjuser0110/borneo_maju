<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

// Placing / editing own orders: admin + agent
Route::prefix('/order')->as('order.')->middleware(['auth', 'role:1,3'])->group(function() {
    Route::get('/index', 'OrderController@index')->name('index');
    Route::get('/create', 'OrderController@create')->name('create');
    Route::post('/store', 'OrderController@store')->name('store');
    Route::get('/edit/{order}', 'OrderController@edit')->name('edit');
    Route::post('/update/{order}', 'OrderController@update')->name('update');
    Route::post('/destroy/{order}', 'OrderController@destroy')->name('destroy');
});

// Viewing details: everyone logged in (agents only their own orders — checked in controller)
Route::prefix('/order')->as('order.')->middleware(['auth', 'role:1,2,3'])->group(function() {
    Route::get('/view_details/{order}', 'OrderController@view_details')->name('view_details');
});

// Processing orders: admin + staff
Route::prefix('/order')->as('order.')->middleware(['auth', 'role:1,2'])->group(function() {
    Route::get('/pending', 'OrderController@pending')->name('pending');
    Route::get('/view/{order}', 'OrderController@view')->name('view');
    Route::post('/pending_update/{order}', 'OrderController@pending_update')->name('pending_update');
});

// Admin only
Route::prefix('/order')->as('order.')->middleware(['auth', 'role:1'])->group(function() {
    Route::post('/revert_status/{order}', 'OrderController@revert_status')->name('revert_status');
    Route::get('/update-order-details/{order}', 'OrderController@updateOrderDetails')->name('updateOrderDetails');
});
