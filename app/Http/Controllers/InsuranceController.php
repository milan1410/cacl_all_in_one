<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Postal Life Insurance (PLI) Calculation.
     *
     * <p>This API endpoint calculates the expected maturity amount and benefits for Postal Life Insurance policies. Users need to input policy details such as sum assured and premium term.</p>
     */
    public function calculatePLI(Request $request)
    {
        $request->validate([
            'sum_assured' => 'required|numeric|min:10000',
            'age' => 'required|integer|min:18',
            'term' => 'required|integer|min:5'
        ]);

        $sumAssured = $request->input('sum_assured');
        $age = $request->input('age');
        $term = $request->input('term');

        // Example premium rate logic (simplified, replace with actual logic)
        $premiumRate = ($age < 35) ? 0.02 : 0.03;
        $premium = $sumAssured * $premiumRate;
        $maturityAmount = $sumAssured + ($sumAssured * 0.05 * $term); // Example maturity calculation

        return response()->json([
            'sum_assured' => $sumAssured,
            'age' => $age,
            'term' => $term,
            'premium' => round($premium, 2),
            'maturity_amount' => round($maturityAmount, 2)
        ]);
    }

    /**
     * Rural Postal Life Insurance (RPLI) Calculation.
     *
     * <p>Calculate the maturity benefits and premium structure for RPLI policies. Input policy details such as sum assured and policy term to receive comprehensive outputs.</p>
     */
    public function calculateRPLI(Request $request)
    {
        $request->validate([
            'sum_assured' => 'required|numeric|min:5000',
            'age' => 'required|integer|min:18',
            'term' => 'required|integer|min:5'
        ]);

        $sumAssured = $request->input('sum_assured');
        $age = $request->input('age');
        $term = $request->input('term');

        // Example RPLI rate logic (replace with actual logic)
        $premiumRate = ($age < 30) ? 0.015 : 0.025;
        $premium = $sumAssured * $premiumRate;
        $maturityAmount = $sumAssured + ($sumAssured * 0.04 * $term); // Example maturity calculation

        return response()->json([
            'sum_assured' => $sumAssured,
            'age' => $age,
            'term' => $term,
            'premium' => round($premium, 2),
            'maturity_amount' => round($maturityAmount, 2)
        ]);
    }

    /**
     * Pradhan Mantri Jeevan Jyoti Bima Yojana (PMJJBY) Calculation.
     *
     * <p>This endpoint calculates the premium and coverage benefits for PMJJBY, helping users understand their insurance plan for life cover.</p>
     */
    public function calculatePMJJBY(Request $request)
    {
        $request->validate([
            'age' => 'required|integer|min:18|max:50'
        ]);

        $age = $request->input('age');
        $sumAssured = 200000; // PMJJBY provides a fixed sum assured of Rs. 2 lakhs.
        $annualPremium = 436; // Fixed annual premium for PMJJBY.

        return response()->json([
            'age' => $age,
            'sum_assured' => $sumAssured,
            'annual_premium' => $annualPremium
        ]);
    }

    /**
     * Pradhan Mantri Suraksha Bima Yojana (PMSBY) Calculation.
     *
     * <p>Calculate the benefits and premium for PMSBY, which provides accidental death and disability cover. This endpoint helps users estimate potential coverage and payment details.</p>
     */
    public function calculatePMSBY(Request $request)
    {
        $request->validate([
            'age' => 'required|integer|min:18|max:70'
        ]);

        $age = $request->input('age');
        $sumAssured = 200000; // PMSBY provides a fixed sum assured of Rs. 2 lakhs.
        $annualPremium = 20; // Fixed annual premium for PMSBY.

        return response()->json([
            'age' => $age,
            'sum_assured' => $sumAssured,
            'annual_premium' => $annualPremium
        ]);
    }
}
