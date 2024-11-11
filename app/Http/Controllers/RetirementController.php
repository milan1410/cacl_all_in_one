<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RetirementController extends Controller
{
    /**
     * National Pension System (NPS) Calculation.
     *
     * <p>Calculate the future value and potential retirement benefits of an NPS investment. The endpoint requires inputs such as contribution amount, duration, and interest rate to provide an estimate.</p>
     */
    public function calculateNPS(Request $request)
    {
        $request->validate([
            'monthly_contribution' => 'required|numeric|min:0',
            'annual_return_rate' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1'
        ]);

        $P = $request->input('monthly_contribution');
        $r = $request->input('annual_return_rate') / 100 / 12;
        $n = $request->input('years') * 12;

        $totalCorpus = $P * ((pow(1 + $r, $n) - 1) / $r) * (1 + $r);

        return response()->json([
            'monthly_contribution' => $P,
            'total_corpus' => round($totalCorpus, 2)
        ]);
    }

    /**
     * Employee Provident Fund (EPF) Calculation.
     *
     * <p>This endpoint calculates the maturity amount of an EPF investment. It takes inputs such as monthly contributions, employer contributions, and applicable interest rates to deliver results.</p>
     */
    public function calculateEPF(Request $request)
    {
        $request->validate([
            'monthly_contribution' => 'required|numeric|min:0',
            'annual_interest_rate' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1'
        ]);

        $P = $request->input('monthly_contribution');
        $r = $request->input('annual_interest_rate') / 100 / 12;
        $n = $request->input('years') * 12;

        $totalAmount = $P * ((pow(1 + $r, $n) - 1) / $r) * (1 + $r);

        return response()->json([
            'monthly_contribution' => $P,
            'epf_total' => round($totalAmount, 2)
        ]);
    }

    /**
     * Atal Pension Scheme (APS) Calculation.
     *
     * <p>Calculate the pension benefits under the Atal Pension Scheme, using inputs like monthly contribution, age, and tenure. The endpoint helps users estimate the pension amount they will receive.</p>
     */
    public function calculateAPS(Request $request)
    {
        $request->validate([
            'monthly_contribution' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1'
        ]);

        // Simplified pension calculation logic
        $P = $request->input('monthly_contribution');
        $n = $request->input('years') * 12;

        $pensionAmount = $P * $n; // Simple accumulation

        return response()->json([
            'monthly_contribution' => $P,
            'estimated_pension' => round($pensionAmount, 2)
        ]);
    }

    /**
     * Pradhan Mantri Shram Yogi Maan-dhan (PM-SYM) Calculation.
     *
     * <p>This API calculates the expected pension benefits under the PM-SYM scheme. It requires inputs such as age at enrollment and monthly contribution to estimate future pension benefits.</p>
     */
    public function calculatePMSYM(Request $request)
    {
        $request->validate([
            'monthly_contribution' => 'required|numeric|min:0',
            'age_of_entry' => 'required|integer|min:18|max:40'
        ]);

        $P = $request->input('monthly_contribution');
        $age = $request->input('age_of_entry');
        $years = 60 - $age; // Years left until retirement

        $totalContribution = $P * $years * 12;

        return response()->json([
            'monthly_contribution' => $P,
            'total_contribution' => round($totalContribution, 2)
        ]);
    }

    /**
     * Gratuity Calculation.
     *
     * <p>Calculate the gratuity amount an employee is entitled to receive based on their salary and years of service. This endpoint helps users plan for retirement or job transition benefits.</p>
     */
    public function calculateGratuity(Request $request)
    {
        $request->validate([
            'last_drawn_salary' => 'required|numeric|min:0',
            'years_of_service' => 'required|integer|min:5'
        ]);

        $salary = $request->input('last_drawn_salary');
        $years = $request->input('years_of_service');

        $gratuity = ($salary * $years * 15) / 26;

        return response()->json([
            'last_drawn_salary' => $salary,
            'years_of_service' => $years,
            'gratuity_amount' => round($gratuity, 2)
        ]);
    }
}
