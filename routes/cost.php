<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/cost')->as('cost.')->middleware(['auth', 'role:1'])->group(function() {
    Route::get('/index', 'CostController@index')->name('index');
    Route::get('/create', 'CostController@create')->name('create');
    Route::post('/store', 'CostController@store')->name('store');
    Route::get('/edit/{cost}', 'CostController@edit')->name('edit');
    Route::post('/update/{cost}', 'CostController@update')->name('update');
    Route::post('/destroy/{cost}', 'CostController@destroy')->name('destroy');
});
