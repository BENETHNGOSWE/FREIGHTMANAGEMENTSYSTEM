<?php
use Illuminate\Support\Facades\Route;

Route::prefix('/')
    ->middleware('auth')
    ->group(function () { 


        Route::get('financial-reports', [App\Http\Controllers\Chiefoperation\FinancialreportController::class, 'index'])->name('chiefoperation.list.financialreport');
        Route::get('/financial-reports/export', [App\Http\Controllers\Chiefoperation\FinancialreportController::class, 'exportExcel'])->name('chiefoperation.excel.financialreport');





        
    });