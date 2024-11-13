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

    public function calculateEMI(Request $request)
    {
        // Validate the input data
        $request->validate([
            'loan_amount' => 'required|numeric|min:1',
            'annual_interest_rate' => 'required|numeric|min:0',
            'loan_term_years' => 'required|integer|min:1',
        ]);

        $loanAmount = $request->input('loan_amount');
        $annualInterestRate = $request->input('annual_interest_rate'); // Annual interest rate in percentage
        $loanTermYears = $request->input('loan_term_years');
        
        // Convert annual interest rate to monthly and express as a decimal
        $monthlyInterestRate = ($annualInterestRate / 100) / 12;
        
        // Total number of payments (months)
        $totalPayments = $loanTermYears * 12;
        
        // EMI formula: EMI = [P * r * (1 + r)^n] / [(1 + r)^n - 1]
        $emi = $loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalPayments) /
               (pow(1 + $monthlyInterestRate, $totalPayments) - 1);
        
        // Calculate total payment and total interest
        $totalPayment = $emi * $totalPayments;
        $totalInterest = $totalPayment - $loanAmount;
        
        // Return the results as JSON
        return response()->json([
            'loan_amount' => round($loanAmount, 2),
            'annual_interest_rate' => $annualInterestRate,
            'loan_term_years' => $loanTermYears,
            'emi' => round($emi, 2),
            'total_payment' => round($totalPayment, 2),
            'total_interest' => round($totalInterest, 2),
        ]);
    }

    public function calculateGST(Request $request)
    {
        // Validate the input data
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0',
        ]);

        $amount = $request->input('amount');
        $gstRate = $request->input('gst_rate'); // GST rate in percentage

        // Calculate GST amount and total amount including GST
        $gstAmount = ($amount * $gstRate) / 100;
        $totalAmount = $amount + $gstAmount;

        // Return the results as JSON
        return response()->json([
            'original_amount' => round($amount, 2),
            'gst_rate' => $gstRate,
            'gst_amount' => round($gstAmount, 2),
            'total_amount_including_gst' => round($totalAmount, 2),
        ]);
    }
    
    public function calculateCAGR(Request $request)
    {
        // Validate the input data
        $request->validate([
            'initial_value' => 'required|numeric|min:0',
            'final_value' => 'required|numeric|min:0',
            'years' => 'required|numeric|min:1',
        ]);

        $initialValue = $request->input('initial_value');
        $finalValue = $request->input('final_value');
        $years = $request->input('years');

        // Calculate CAGR: CAGR = [(Final Value / Initial Value)^(1/Years)] - 1
        $cagr = (pow($finalValue / $initialValue, 1 / $years) - 1) * 100;

        // Return the results as JSON
        return response()->json([
            'initial_value' => round($initialValue, 2),
            'final_value' => round($finalValue, 2),
            'years' => $years,
            'cagr' => round($cagr, 2) . '%',
        ]);
    }

    public function calculateGratuity(Request $request)
    {
        // Validate the input data
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'years_of_service' => 'required|numeric|min:0',
        ]);

        $basicSalary = $request->input('basic_salary');
        $yearsOfService = $request->input('years_of_service');

        // Gratuity formula: Gratuity = (15/26) * Basic Salary * Years of Service
        $gratuity = (15 / 26) * $basicSalary * $yearsOfService;

        // Return the results as JSON
        return response()->json([
            'basic_salary' => round($basicSalary, 2),
            'years_of_service' => $yearsOfService,
            'gratuity' => round($gratuity, 2),
        ]);
    }

    public function calculateHRA(Request $request)
    {
        // Validate the input
        $request->validate([
            'basic_salary' => 'required|numeric',
            'rent' => 'required|numeric',
            'other_allowances' => 'required|numeric',
            'city_type' => 'required|in:metro,non-metro', // Example: metro or non-metro city
        ]);

        // Input data
        $basicSalary = $request->input('basic_salary');
        $rent = $request->input('rent');
        $otherAllowances = $request->input('other_allowances');
        $cityType = $request->input('city_type');

        // Calculate HRA components
        $hra = $this->calculateHRAAmount($basicSalary, $rent, $cityType);

        // Return the result as a JSON response
        return response()->json([
            'basic_salary' => $basicSalary,
            'rent' => $rent,
            'other_allowances' => $otherAllowances,
            'hra' => $hra,
        ]);
    }

    /**
     * Helper method to calculate HRA amount based on basic salary, rent, and city type.
     *
     * @param  float  $basicSalary
     * @param  float  $rent
     * @param  string  $cityType
     * @return float
     */
    private function calculateHRAAmount($basicSalary, $rent, $cityType)
    {
        $hra = 0;

        // Formula for HRA calculation based on city type
        if ($cityType == 'metro') {
            // 50% of basic salary in metro cities
            $hra = min($rent, 0.50 * $basicSalary);
        } else {
            // 40% of basic salary in non-metro cities
            $hra = min($rent, 0.40 * $basicSalary);
        }

        return $hra;
    }

    public function calculateAPY(Request $request)
    {
        // Validate the input
        $request->validate([
            'principal' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'compounds_per_year' => 'required|integer|min:1',
        ]);

        // Input data
        $principal = $request->input('principal');
        $interestRate = $request->input('interest_rate') / 100;  // Convert to decimal
        $compoundsPerYear = $request->input('compounds_per_year');

        // Calculate APY
        $apy = $this->calculateAPYAmount($principal, $interestRate, $compoundsPerYear);

        // Return the result as a JSON response
        return response()->json([
            'principal' => $principal,
            'interest_rate' => $request->input('interest_rate'),
            'compounds_per_year' => $compoundsPerYear,
            'apy' => round($apy, 4)  // Round to 4 decimal places
        ]);
    }

    /**
     * Helper method to calculate the APY (Annual Percentage Yield).
     *
     * @param  float  $principal
     * @param  float  $interestRate
     * @param  int  $compoundsPerYear
     * @return float
     */
    private function calculateAPYAmount($principal, $interestRate, $compoundsPerYear)
    {
        // Apply the APY formula
        $apy = pow(1 + ($interestRate / $compoundsPerYear), $compoundsPerYear) - 1;
        return $apy * 100;  // Return APY as percentage
    }

    public function calculateRetirement(Request $request)
    {
        // Validate the input data
        $request->validate([
            'retirement_goal' => 'required|numeric|min:0',
            'current_savings' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'years_until_retirement' => 'required|integer|min:1',
        ]);

        // Input data from the request
        $retirementGoal = $request->input('retirement_goal');
        $currentSavings = $request->input('current_savings');
        $interestRate = $request->input('interest_rate') / 100;  // Convert to decimal
        $yearsUntilRetirement = $request->input('years_until_retirement');

        // Calculate the number of months until retirement
        $monthsUntilRetirement = $yearsUntilRetirement * 12;

        // Calculate the monthly interest rate
        $monthlyInterestRate = $interestRate / 12;

        // Calculate the future value needed (subtract current savings from retirement goal)
        $futureValueNeeded = $retirementGoal - $currentSavings;

        // Calculate the monthly savings required (using the formula)
        if ($monthlyInterestRate > 0) {
            $monthlyContribution = ($futureValueNeeded * $monthlyInterestRate) / (pow(1 + $monthlyInterestRate, $monthsUntilRetirement) - 1);
        } else {
            // If interest rate is 0, just divide by months until retirement
            $monthlyContribution = $futureValueNeeded / $monthsUntilRetirement;
        }

        // Return the result as a JSON response
        return response()->json([
            'retirement_goal' => $retirementGoal,
            'current_savings' => $currentSavings,
            'interest_rate' => $request->input('interest_rate'),
            'years_until_retirement' => $yearsUntilRetirement,
            'monthly_contribution_required' => round($monthlyContribution, 2),
        ]);
    }

}
