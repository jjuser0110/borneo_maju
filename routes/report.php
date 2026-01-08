<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/report')->as('report.')->middleware(['auth'])->group(function() {
    Route::get('/sales_report', 'ReportController@sales_report')->name('sales_report');
});
