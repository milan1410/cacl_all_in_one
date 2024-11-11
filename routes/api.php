<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\PostOfficeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/calculate', [CalculatorController::class, 'calculate']);
//BANK
Route::get('/loan-basic-calculate', [BankController::class, 'calculateLoanBasic']);
Route::get('/loan-advanced-calculate', [BankController::class, 'calculateLoanAdvanced']);
Route::get('/fixed-deposit-calculate', [BankController::class, 'calculateFD']);
Route::get('/cumulative-fixed-deposit-calculate', [BankController::class, 'calculateCumulativeFD']);
Route::get('/recurring-deposit-calculate', [BankController::class, 'calculateRD']);
Route::get('/interest-rates', [BankController::class, 'getInterestRates']);

//Bank & Post Office
Route::get('/ppf-fixed-calculate', [PostOfficeController::class, 'calculatePPFFixed']);
Route::post('/ppf-variable-calculate', [PostOfficeController::class, 'calculatePPFVariable']);
Route::get('/ssy-calculate', [PostOfficeController::class, 'calculateSSY']);
Route::get('/scss-calculate', [PostOfficeController::class, 'calculateSCSS']);
Route::get('/kvp-calculate', [PostOfficeController::class, 'calculateKVP']);
Route::get('/mssc-calculate', [PostOfficeController::class, 'calculateMahilaSamman']);

//Post Office
Route::get('/mis-calculate', [PostOfficeController::class, 'calculateMIS']);
Route::get('/rd-calculate', [PostOfficeController::class, 'calculateRD']);
Route::get('/td-calculate', [PostOfficeController::class, 'calculateTD']);
Route::get('/nsc-calculate', [PostOfficeController::class, 'calculateNSC']);
Route::get('/interest-rates', [PostOfficeController::class, 'getInterestRates']);
