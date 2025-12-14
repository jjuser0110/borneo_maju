<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/staff')->as('staff.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'StaffController@index')->name('index');
    Route::get('/create', 'StaffController@create')->name('create');
    Route::post('/store', 'StaffController@store')->name('store');
    Route::get('/edit/{staff}', 'StaffController@edit')->name('edit');
    Route::post('/update/{staff}', 'StaffController@update')->name('update');
    Route::get('/destroy/{staff}', 'StaffController@destroy')->name('destroy');
});
