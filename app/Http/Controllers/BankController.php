<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Basic Loan Calculation.
     *
     * <p>This API endpoint calculates the basic loan parameters, such as monthly payments and interest based on user inputs like loan amount, interest rate, and term. It is ideal for simple loan evaluation.</p>
     */
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

    /**
     * Advanced Loan Calculation.
     *
     * <p>This endpoint handles more complex loan calculations that might include additional factors such as fees, compound interest, or custom repayment structures. The method processes detailed loan data to deliver accurate, in-depth results.</p>
     */
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

    /**
     * Fixed Deposit Calculation.
     *
     * <p>This API calculates the maturity amount and interest earned for a fixed deposit. Input parameters such as the principal amount, rate of interest, and deposit period are required to generate accurate outputs.</p>
     */
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

    /**
     * Cumulative Fixed Deposit Calculation.
     *
     * <p>This endpoint calculates the final amount of a cumulative fixed deposit, considering compounded interest over the chosen term. It helps users understand the total return on their deposit investments.</p>
     */
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

    /**
     * Recurring Deposit Calculation.
     *
     * <p>Calculate the future value of a recurring deposit with this API. Users need to provide the monthly installment amount, interest rate, and duration to receive a result showing the total maturity value.</p>
     */
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

    /**
     * Fetch Interest Rates.
     *
     * <p>This API endpoint retrieves current interest rates applicable for different financial products. It ensures users have access to up-to-date information for better decision-making regarding investments or loans.</p>
     */
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
