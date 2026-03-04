<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/bank_setting')->as('bank_setting.')->middleware(['auth'])->group(function() {
    Route::get('/index', 'BankSettingController@index')->name('index');
    Route::get('/create', 'BankSettingController@create')->name('create');
    Route::post('/store', 'BankSettingController@store')->name('store');
    Route::get('/edit/{bank_setting}', 'BankSettingController@edit')->name('edit');
    Route::post('/update/{bank_setting}', 'BankSettingController@update')->name('update');
    Route::get('/destroy/{bank_setting}', 'BankSettingController@destroy')->name('destroy');
    Route::post('/adjust_money', 'BankSettingController@adjust_money')->name('adjust_money');
    Route::get('/viewlog/{bank_setting}', 'BankSettingController@viewlog')->name('viewlog');
    Route::post('/add-stock', 'BankSettingController@addStock')->name('addStock');
    Route::post('/update-stock-balance', 'BankSettingController@updateStockBalance')->name('update_stock_balance');

    Route::get('/view-stock-log/{stock}', 'BankSettingController@view_stock_log')->name('view_stock_log');
    Route::get('/destroy-stock/{stock}', 'BankSettingController@destroy_stock')->name('destroy_stock');
});
