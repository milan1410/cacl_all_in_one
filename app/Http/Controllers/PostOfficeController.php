<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostOfficeController extends Controller
{
    public function calculatePPFFixed(Request $request)
    {
        $annualContribution = $request->input('annual_contribution'); // Fixed amount deposited each year
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Term in years

        if (!is_numeric($annualContribution) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        $r = $annualInterestRate / 100; // Convert to decimal
        $maturityAmount = $annualContribution * ((pow(1 + $r, $termInYears) - 1) / $r) * (1 + $r);

        $totalDeposits = $annualContribution * $termInYears;
        $totalInterest = $maturityAmount - $totalDeposits;

        return response()->json([
            'annual_contribution' => $annualContribution,
            'total_deposits' => round($totalDeposits, 2),
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function calculatePPFVariable(Request $request)
    {
        $contributions = $request->input('contributions'); // Array of annual contributions
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
    
        if (!is_array($contributions) || !is_numeric($annualInterestRate)) {
            return response()->json(['error' => 'Inputs must be valid. Contributions should be an array and interest rate a number.'], 400);
        }
    
        $r = $annualInterestRate / 100; // Convert to decimal
        $termInYears = count($contributions); // Number of years based on the contributions array
        $maturityAmount = 0;
    
        for ($i = 0; $i < $termInYears; $i++) {
            $maturityAmount += $contributions[$i] * pow(1 + $r, $termInYears - $i);
        }
    
        $totalDeposits = array_sum($contributions);
        $totalInterest = $maturityAmount - $totalDeposits;
    
        return response()->json([
            'contributions' => $contributions,
            'total_deposits' => round($totalDeposits, 2),
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function calculateSSY(Request $request)
    {
        $annualContribution = $request->input('annual_contribution'); // Amount deposited each year
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years', 21); // Default term is 21 years for SSY

        if (!is_numeric($annualContribution) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        $r = $annualInterestRate / 100; // Convert to decimal
        $maturityAmount = $annualContribution * ((pow(1 + $r, $termInYears) - 1) / $r) * (1 + $r);

        $totalDeposits = $annualContribution * $termInYears;
        $totalInterest = $maturityAmount - $totalDeposits;

        return response()->json([
            'annual_contribution' => $annualContribution,
            'total_deposits' => round($totalDeposits, 2),
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function calculateSCSS(Request $request)
    {
        $principalAmount = $request->input('principal_amount'); // Initial investment
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years', 5); // Term in years, default is 5

        if (!is_numeric($principalAmount) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Convert annual rate to a quarterly rate
        $quarterlyRate = $annualInterestRate / 4 / 100;
        $quarterlyInterest = $principalAmount * $quarterlyRate;
        $totalInterest = $quarterlyInterest * $termInYears * 4; // Total interest over the period

        $maturityAmount = $principalAmount + $totalInterest;

        return response()->json([
            'principal_amount' => $principalAmount,
            'quarterly_interest' => round($quarterlyInterest, 2),
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function calculateKVP(Request $request)
    {
        $principalAmount = $request->input('principal_amount'); // Initial investment
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        
        if (!is_numeric($principalAmount) || !is_numeric($annualInterestRate)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        $r = $annualInterestRate / 100; // Convert to decimal
        $yearsToDouble = log(2) / log(1 + $r); // Calculate the number of years for the investment to double

        // Calculate the maturity amount after the calculated period
        $maturityAmount = $principalAmount * pow(1 + $r, $yearsToDouble);

        return response()->json([
            'principal_amount' => $principalAmount,
            'annual_interest_rate' => $annualInterestRate,
            'years_to_double' => round($yearsToDouble, 2),
            'maturity_amount' => round($maturityAmount, 2)
        ]);
    }

    public function calculateMahilaSamman(Request $request)
    {
        $principalAmount = $request->input('principal_amount'); // Initial investment
        $annualInterestRate = $request->input('annual_interest_rate', 7.5); // Default rate set at 7.5%
        $tenureInYears = 2; // Fixed tenure of 2 years

        if (!is_numeric($principalAmount) || $principalAmount <= 0) {
            return response()->json(['error' => 'Principal amount must be a valid positive number'], 400);
        }

        // Quarterly interest rate calculation
        $quarterlyRate = $annualInterestRate / 4 / 100;
        $totalQuarters = $tenureInYears * 4;

        $finalAmount = $principalAmount;

        // Compound interest for each quarter
        for ($i = 0; $i < $totalQuarters; $i++) {
            $finalAmount += $finalAmount * $quarterlyRate;
        }

        return response()->json([
            'principal_amount' => $principalAmount,
            'annual_interest_rate' => $annualInterestRate,
            'tenure_in_years' => $tenureInYears,
            'maturity_amount' => round($finalAmount, 2)
        ]);
    }

    public function calculateMIS(Request $request)
    {
        $principalAmount = $request->input('principal_amount');
        $annualInterestRate = $request->input('annual_interest_rate');

        if (!is_numeric($principalAmount) || !is_numeric($annualInterestRate) || $principalAmount <= 0) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $monthlyInterest = ($principalAmount * ($annualInterestRate / 100)) / 12;

        return response()->json([
            'principal_amount' => $principalAmount,
            'annual_interest_rate' => $annualInterestRate,
            'monthly_interest' => round($monthlyInterest, 2)
        ]);
    }
    
    public function calculateRD(Request $request)
    {
        $monthlyDeposit = $request->input('monthly_deposit');
        $annualInterestRate = $request->input('annual_interest_rate');
        $tenureInYears = $request->input('tenure', 5);

        $r = ($annualInterestRate / 4) / 100;
        $n = $tenureInYears * 4;
        $A = $monthlyDeposit * ((pow(1 + $r, $n) - 1) / (1 - pow(1 + $r, -1)));

        return response()->json([
            'monthly_deposit' => $monthlyDeposit,
            'annual_interest_rate' => $annualInterestRate,
            'tenure_in_years' => $tenureInYears,
            'maturity_amount' => round($A, 2)
        ]);
    }
    public function calculateTD(Request $request)
    {
        $principalAmount = $request->input('principal_amount');
        $annualInterestRate = $request->input('annual_interest_rate');
        $tenureInYears = $request->input('tenure');

        $A = $principalAmount * pow(1 + ($annualInterestRate / 100), $tenureInYears);

        return response()->json([
            'principal_amount' => $principalAmount,
            'annual_interest_rate' => $annualInterestRate,
            'tenure_in_years' => $tenureInYears,
            'maturity_amount' => round($A, 2)
        ]);
    }
    public function calculateNSC(Request $request)
    {
        $principalAmount = $request->input('principal_amount');
        $annualInterestRate = $request->input('annual_interest_rate');
        $tenureInYears = 5; // Fixed tenure for NSC

        $A = $principalAmount * pow(1 + ($annualInterestRate / 100), $tenureInYears);

        return response()->json([
            'principal_amount' => $principalAmount,
            'annual_interest_rate' => $annualInterestRate,
            'tenure_in_years' => $tenureInYears,
            'maturity_amount' => round($A, 2)
        ]);
    }
    public function getInterestRates()
    {
        $rates = [
            'MIS' => 7.4,
            'RD' => 5.8,
            'TD 1 Year' => 6.6,
            'TD 2 Year' => 6.8,
            'TD 3 Year' => 6.9,
            'TD 5 Year' => 7.0,
            'NSC' => 7.7,
        ];

        return response()->json($rates);
    }

    
}
