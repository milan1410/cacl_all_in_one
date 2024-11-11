<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankController extends Controller
{
    public function calculateLoanBasic(Request $request)
    {
        $principal = $request->input('principal'); // Loan amount
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Loan term in years

        if (!is_numeric($principal) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Convert annual interest rate to monthly and percentage to decimal
        $monthlyInterestRate = ($annualInterestRate / 100) / 12;
        $numberOfPayments = $termInYears * 12;

        if ($monthlyInterestRate == 0) {
            // Handle zero interest rate (simple division)
            $monthlyPayment = $principal / $numberOfPayments;
        } else {
            // Calculate monthly payment using the loan formula
            $monthlyPayment = $principal * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $numberOfPayments) /
                (pow(1 + $monthlyInterestRate, $numberOfPayments) - 1);
        }

        return response()->json([
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($monthlyPayment * $numberOfPayments, 2),
            'total_interest' => round(($monthlyPayment * $numberOfPayments) - $principal, 2),
        ]);
    }

    public function calculateLoanAdvanced(Request $request)
    {
        $principal = $request->input('principal'); // Loan amount
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Loan term in years
        $extraPayment = $request->input('extra_payment', 0); // Optional extra monthly payment

        if (!is_numeric($principal) || !is_numeric($annualInterestRate) || !is_numeric($termInYears) || !is_numeric($extraPayment)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Convert annual interest rate to monthly and percentage to decimal
        $monthlyInterestRate = ($annualInterestRate / 100) / 12;
        $numberOfPayments = $termInYears * 12;

        if ($monthlyInterestRate == 0) {
            $monthlyPayment = $principal / $numberOfPayments;
        } else {
            $monthlyPayment = $principal * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $numberOfPayments) /
                (pow(1 + $monthlyInterestRate, $numberOfPayments) - 1);
        }

        $totalInterest = 0;
        $amortizationSchedule = [];
        $remainingBalance = $principal;

        for ($i = 1; $i <= $numberOfPayments && $remainingBalance > 0; $i++) {
            $interestPayment = $remainingBalance * $monthlyInterestRate;
            $principalPayment = $monthlyPayment - $interestPayment + $extraPayment;

            if ($remainingBalance < $principalPayment) {
                $principalPayment = $remainingBalance;
            }

            $remainingBalance -= $principalPayment;
            $totalInterest += $interestPayment;

            $amortizationSchedule[] = [
                'month' => $i,
                'interest_payment' => round($interestPayment, 2),
                'principal_payment' => round($principalPayment, 2),
                'remaining_balance' => round($remainingBalance, 2),
            ];
        }

        return response()->json([
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($monthlyPayment * min($numberOfPayments, $i - 1), 2),
            'total_interest' => round($totalInterest, 2),
            'amortization_schedule' => $amortizationSchedule,
        ]);
    }

    public function calculateFD(Request $request)
    {
        $principal = $request->input('principal'); // Deposit amount
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Term in years
        $payoutFrequency = $request->input('payout_frequency', 'monthly'); // Frequency of interest payout: 'monthly', 'quarterly', 'annually'

        if (!is_numeric($principal) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Map payout frequencies to the number of times interest is paid per year
        $payoutsPerYearMap = [
            'monthly' => 12,
            'quarterly' => 4,
            'annually' => 1,
        ];

        if (!array_key_exists($payoutFrequency, $payoutsPerYearMap)) {
            return response()->json(['error' => 'Invalid payout frequency'], 400);
        }

        $payoutsPerYear = $payoutsPerYearMap[$payoutFrequency];
        $interestRatePerPayout = ($annualInterestRate / 100) / $payoutsPerYear;
        $totalPayouts = $termInYears * $payoutsPerYear;

        $totalInterest = 0;
        $amortizationSchedule = [];
        $remainingBalance = $principal;

        for ($i = 1; $i <= $totalPayouts; $i++) {
            $interestPayment = $remainingBalance * $interestRatePerPayout;
            $totalInterest += $interestPayment;

            $amortizationSchedule[] = [
                'payout_number' => $i,
                'interest_payment' => round($interestPayment, 2),
                'remaining_balance' => round($remainingBalance, 2),
            ];
        }

        return response()->json([
            'principal' => $principal,
            'total_interest' => round($totalInterest, 2),
            'total_payouts' => $totalPayouts,
            'amortization_schedule' => $amortizationSchedule,
        ]);
    }

    public function calculateCumulativeFD(Request $request)
    {
        $principal = $request->input('principal'); // Initial deposit amount
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Term in years
        $compoundingFrequency = $request->input('compounding_frequency', 'quarterly'); // 'monthly', 'quarterly', 'annually'

        if (!is_numeric($principal) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Map compounding frequencies to the number of times interest is compounded per year
        $compoundingMap = [
            'monthly' => 12,
            'quarterly' => 4,
            'annually' => 1,
        ];

        if (!array_key_exists($compoundingFrequency, $compoundingMap)) {
            return response()->json(['error' => 'Invalid compounding frequency'], 400);
        }

        $compoundingPeriods = $compoundingMap[$compoundingFrequency];
        $ratePerPeriod = ($annualInterestRate / 100) / $compoundingPeriods;
        $totalPeriods = $termInYears * $compoundingPeriods;

        // Compound Interest Formula: A = P(1 + r/n)^(nt)
        $maturityAmount = $principal * pow(1 + $ratePerPeriod, $totalPeriods);
        $totalInterest = $maturityAmount - $principal;

        return response()->json([
            'principal' => $principal,
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'compounding_frequency' => $compoundingFrequency,
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function calculateRD(Request $request)
    {
        $monthlyInstallment = $request->input('monthly_installment'); // Amount deposited monthly
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $termInYears = $request->input('term_in_years'); // Term in years
        $compoundingFrequency = $request->input('compounding_frequency', 'quarterly'); // 'monthly', 'quarterly', 'annually'

        if (!is_numeric($monthlyInstallment) || !is_numeric($annualInterestRate) || !is_numeric($termInYears)) {
            return response()->json(['error' => 'Inputs must be valid numbers'], 400);
        }

        // Map compounding frequencies to the number of times interest is compounded per year
        $compoundingMap = [
            'monthly' => 12,
            'quarterly' => 4,
            'annually' => 1,
        ];

        if (!array_key_exists($compoundingFrequency, $compoundingMap)) {
            return response()->json(['error' => 'Invalid compounding frequency'], 400);
        }

        $n = $compoundingMap[$compoundingFrequency];
        $r = ($annualInterestRate / 100) / $n; // Rate per period
        $totalPeriods = $termInYears * $n;

        $maturityAmount = 0;

        for ($i = 0; $i < $totalPeriods; $i++) {
            $maturityAmount += $monthlyInstallment * pow(1 + $r, $totalPeriods - $i);
        }

        $totalDeposits = $monthlyInstallment * $termInYears * 12;
        $totalInterest = $maturityAmount - $totalDeposits;

        return response()->json([
            'monthly_installment' => $monthlyInstallment,
            'total_deposits' => round($totalDeposits, 2),
            'total_interest' => round($totalInterest, 2),
            'maturity_amount' => round($maturityAmount, 2),
            'compounding_frequency' => $compoundingFrequency,
            'term_in_years' => $termInYears,
            'annual_interest_rate' => $annualInterestRate,
        ]);
    }

    public function getInterestRates()
    {
        $rates = [
            'Loan' => 7.5, // Example rate for loan
            'FD' => 6.0,   // Example rate for fixed deposit
            'RD' => 5.8,   // Example rate for recurring deposit
        ];

        return response()->json($rates);
    }
}
