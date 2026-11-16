<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->as('admin.')->middleware(['auth', 'role:1'])->group(function() {
    Route::get('/index', 'AdminController@index')->name('index');
    Route::get('/create', 'AdminController@create')->name('create');
    Route::post('/store', 'AdminController@store')->name('store');
    Route::get('/edit/{admin}', 'AdminController@edit')->name('edit');
    Route::post('/update/{admin}', 'AdminController@update')->name('update');
    Route::post('/destroy/{admin}', 'AdminController@destroy')->name('destroy');
});
