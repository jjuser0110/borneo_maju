<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/agent')->as('agent.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'AgentController@index')->name('index');
    Route::get('/create', 'AgentController@create')->name('create');
    Route::post('/store', 'AgentController@store')->name('store');
    Route::get('/edit/{agent}', 'AgentController@edit')->name('edit');
    Route::get('/downline/{agent}', 'AgentController@downline')->name('downline');
    Route::post('/update/{agent}', 'AgentController@update')->name('update');
    Route::get('/destroy/{agent}', 'AgentController@destroy')->name('destroy');
});
