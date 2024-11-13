<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BondController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\MutualFundsController;
use App\Http\Controllers\PostOfficeController;
use App\Http\Controllers\RetirementController;
use App\Http\Controllers\TaxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/calculate', [CalculatorController::class, 'calculate']);
//BANK
Route::get('/loan-basic-calculate', [BankController::class, 'calculateLoanBasic']); //Done
Route::get('/loan-advanced-calculate', [BankController::class, 'calculateLoanAdvanced']);
Route::get('/fixed-deposit-calculate', [BankController::class, 'calculateFD']); //Done
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

//Mutual Funds
Route::get('/mutual-funds-overview', [MutualFundsController::class, 'getOverview']);
Route::get('/mutual-funds-top-listing', [MutualFundsController::class, 'getTopListing']);
Route::get('/elss-calculate', [MutualFundsController::class, 'calculateELSS']);
Route::get('/sip-calculate', [MutualFundsController::class, 'calculateSIP']);
Route::get('/swp-calculate', [MutualFundsController::class, 'calculateSWP']);

//Retirement
Route::get('/nps-calculate', [RetirementController::class, 'calculateNPS']);
Route::get('/epf-calculate', [RetirementController::class, 'calculateEPF']);
Route::get('/aps-calculate', [RetirementController::class, 'calculateAPS']);
Route::get('/pm-sym-calculate', [RetirementController::class, 'calculatePMSYM']);
Route::get('/gratuity-calculate', [RetirementController::class, 'calculateGratuity']);

//Tax 
Route::get('/income-tax-calculate', [TaxController::class, 'calculateIncomeTax']);
Route::get('/capital-gains-tax-calculate', [TaxController::class, 'calculateCapitalGainsTax']);

//Insurance 
Route::get('/postal-life-insurance-calculate', [InsuranceController::class, 'calculatePLI']);
Route::get('/rural-postal-life-insurance-calculate', [InsuranceController::class, 'calculateRPLI']);
Route::get('/pm-jeevan-jyoti-bima-calculate', [InsuranceController::class, 'calculatePMJJBY']);
Route::get('/pm-suraksha-bima-calculate', [InsuranceController::class, 'calculatePMSBY']);

//Bonds 
Route::get('/bonds-overview', [BondController::class, 'overview']);
Route::get('/floating-rate-bonds-calculate', [BondController::class, 'calculateFloatingRate']);
Route::get('/sovereign-gold-bonds-calculate', [BondController::class, 'calculateSGB']);
Route::get('/54ec-bonds-info', [BondController::class, 'info54ECBonds']);

//General
Route::get('/compound-interest', [FinanceController::class, 'calculateCompoundInterest']);
Route::get('/simple-interest', [FinanceController::class, 'calculateSimpleInterest']);
Route::get('/inflation', [FinanceController::class, 'calculateInflation']);
