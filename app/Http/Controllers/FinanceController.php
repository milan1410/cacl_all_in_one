<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Compound Interest Calculation.
     *
     * <p>Calculate the future value of an investment or loan using the compound interest formula. This API accepts inputs like principal amount, rate of interest, number of times interest is compounded per year, and duration.</p>
     */
    public function calculateCompoundInterest(Request $request)
    {
        $request->validate([
            'principal' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'time' => 'required|numeric|min:0',
            'compounds_per_year' => 'required|integer|min:1'
        ]);

        $principal = $request->input('principal');
        $rate = $request->input('rate') / 100;
        $time = $request->input('time');
        $n = $request->input('compounds_per_year');

        // Compound interest formula: A = P(1 + r/n)^(nt)
        $futureValue = $principal * pow((1 + $rate / $n), $n * $time);

        return response()->json([
            'principal' => $principal,
            'rate' => $rate * 100,
            'time' => $time,
            'compounds_per_year' => $n,
            'future_value' => round($futureValue, 2)
        ]);
    }

    /**
     * Simple Interest Calculation.
     *
     * <p>Calculate the total interest and final amount based on the simple interest formula. Users need to input principal amount, rate of interest, and time period.</p>
     */
    public function calculateSimpleInterest(Request $request)
    {
        $request->validate([
            'principal' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'time' => 'required|numeric|min:0'
        ]);

        $principal = $request->input('principal');
        $rate = $request->input('rate') / 100;
        $time = $request->input('time');

        // Simple interest formula: SI = P * r * t
        $simpleInterest = $principal * $rate * $time;
        $totalAmount = $principal + $simpleInterest;

        return response()->json([
            'principal' => $principal,
            'rate' => $rate * 100,
            'time' => $time,
            'simple_interest' => round($simpleInterest, 2),
            'total_amount' => round($totalAmount, 2)
        ]);
    }

    /**
     * Inflation Adjustment Calculation.
     *
     * <p>This endpoint calculates the future value of money adjusted for inflation, helping users understand how their purchasing power changes over time based on inflation rates.</p>
     */
    public function calculateInflation(Request $request)
    {
        $request->validate([
            'current_amount' => 'required|numeric|min:0',
            'inflation_rate' => 'required|numeric|min:0',
            'years' => 'required|numeric|min:0'
        ]);

        $currentAmount = $request->input('current_amount');
        $inflationRate = $request->input('inflation_rate') / 100;
        $years = $request->input('years');

        // Future value considering inflation: FV = PV / (1 + r)^t
        $futureValue = $currentAmount / pow((1 + $inflationRate), $years);

        return response()->json([
            'current_amount' => $currentAmount,
            'inflation_rate' => $inflationRate * 100,
            'years' => $years,
            'future_value' => round($futureValue, 2)
        ]);
    }
}
