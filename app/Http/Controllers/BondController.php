<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BondController extends Controller
{
    /**
     * Bonds Overview.
     *
     * <p>This API provides an overview of different types of bonds, including details on interest rates, maturity terms, and investment benefits. It helps users gain insights into bond options available.</p>
     */
    public function overview()
    {
        $bonds = [
            [
                'type' => 'Floating Rate Saving Bonds',
                'description' => 'These bonds have an interest rate that adjusts periodically based on benchmark rates. They are suitable for investors seeking regular income with protection against interest rate fluctuations.'
            ],
            [
                'type' => 'Sovereign Gold Bond Scheme',
                'description' => 'A government bond that offers returns linked to gold prices. It’s an alternative to physical gold investment with added interest income.'
            ],
            [
                'type' => '54EC Bonds (Save Capital Gain Tax)',
                'description' => 'These bonds help in saving long-term capital gain tax under Section 54EC of the Income Tax Act. They are generally issued by entities such as REC and NHAI.'
            ]
        ];

        return response()->json($bonds);
    }

    /**
     * Floating Rate Bonds Calculation.
     *
     * <p>Calculate the returns from floating rate bonds based on interest rate changes and other parameters. This endpoint is useful for understanding the investment potential in variable-rate bonds.</p>
     */
    public function calculateFloatingRate(Request $request)
    {
        $request->validate([
            'principal' => 'required|numeric|min:1000',
            'interest_rate' => 'required|numeric|min:0',
            'period' => 'required|integer|min:1'
        ]);

        $principal = $request->input('principal');
        $rate = $request->input('interest_rate') / 100;
        $period = $request->input('period');

        // Simple interest calculation for example purposes
        $totalInterest = $principal * $rate * $period;
        $totalAmount = $principal + $totalInterest;

        return response()->json([
            'principal' => $principal,
            'interest_rate' => $rate * 100,
            'period' => $period,
            'total_interest' => round($totalInterest, 2),
            'total_amount' => round($totalAmount, 2)
        ]);
    }

    /**
     * Sovereign Gold Bonds (SGB) Calculation.
     *
     * <p>Calculate the expected returns and interest earned from investing in Sovereign Gold Bonds. Inputs include investment amount, tenure, and interest rate.</p>
     */
    public function calculateSGB(Request $request)
    {
        $request->validate([
            'investment_amount' => 'required|numeric|min:1000',
            'gold_price_increase' => 'required|numeric|min:0',
            'period' => 'required|integer|min:1'
        ]);

        $investment = $request->input('investment_amount');
        $goldGrowthRate = $request->input('gold_price_increase') / 100;
        $period = $request->input('period');

        $totalValue = $investment * pow((1 + $goldGrowthRate), $period);
        $interestIncome = ($investment * 0.025 * $period); // 2.5% interest annually

        return response()->json([
            'investment_amount' => $investment,
            'gold_price_increase' => $goldGrowthRate * 100,
            'period' => $period,
            'total_value' => round($totalValue, 2),
            'interest_income' => round($interestIncome, 2),
            'final_value' => round($totalValue + $interestIncome, 2)
        ]);
    }

    /**
     * 54EC Bonds Information.
     *
     * <p>This API provides details on 54EC bonds, which are used for tax-saving purposes on capital gains. It outlines the bond's terms, interest rate, and tax benefits.</p>
     */
    public function info54ECBonds()
    {
        $info = [
            'description' => '54EC bonds are specifically issued by entities like NHAI and REC to help individuals save tax on long-term capital gains. Investments up to Rs. 50 lakh per financial year are eligible, and the bonds have a lock-in period of 5 years.',
            'benefits' => [
                'Save long-term capital gain tax.',
                'Issued by reputed government-backed entities.',
                'Fixed interest rate (typically 5-6%).'
            ]
        ];

        return response()->json($info);
    }
}
