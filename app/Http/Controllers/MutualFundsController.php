<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MutualFundsController extends Controller
{
    /**
     * Mutual Funds Overview.
     *
     * <p>This API endpoint provides an overview of various mutual funds, including performance data, categories, and investment options. It is designed to help users understand available mutual fund products and their key details.</p>
     */
    public function getOverview()
    {
        $overview = [
            'definition' => 'Mutual funds are investment vehicles that pool money from multiple investors to invest in securities like stocks, bonds, and other assets.',
            'types' => [
                'Equity Funds',
                'Debt Funds',
                'Balanced Funds',
                'Index Funds',
                'ELSS (Equity Linked Saving Scheme)'
            ],
            'benefits' => [
                'Diversification',
                'Professional Management',
                'Liquidity',
                'Affordability'
            ],
            'risks' => [
                'Market risk',
                'Interest rate risk',
                'Credit risk'
            ]
        ];

        return response()->json($overview);
    }

    /**
     * Top Mutual Funds Listing.
     *
     * <p>Retrieve a list of top-performing mutual funds based on current market data. This API helps investors identify high-ranking mutual funds for potential investments.</p>
     */
    public function getTopListing()
    {
        $topFunds = [
            [
                'name' => 'ABC Equity Fund',
                'category' => 'Equity',
                'annual_return' => '15%',
                'risk' => 'High'
            ],
            [
                'name' => 'XYZ Debt Fund',
                'category' => 'Debt',
                'annual_return' => '7%',
                'risk' => 'Low'
            ],
            // Add more top funds as needed
        ];

        return response()->json($topFunds);
    }

    /**
     * ELSS (Equity Linked Savings Scheme) Calculation.
     *
     * <p>This endpoint calculates the potential returns for an ELSS investment. Users input the investment amount and tenure to estimate future returns, while considering tax-saving benefits.</p>
     */
    public function calculateELSS(Request $request)
    {
        $request->validate([
            'monthly_investment' => 'required|numeric|min:0',
            'annual_return_rate' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1'
        ]);

        $P = $request->input('monthly_investment');
        $r = $request->input('annual_return_rate') / 100 / 12;
        $n = $request->input('years') * 12;

        $maturityAmount = $P * ((pow(1 + $r, $n) - 1) / $r) * (1 + $r);

        return response()->json([
            'monthly_investment' => $P,
            'maturity_amount' => round($maturityAmount, 2)
        ]);
    }

    /**
     * Systematic Investment Plan (SIP) Calculation.
     *
     * <p>Calculate the future value of a SIP investment based on regular contributions, interest rate, and investment period. This endpoint helps users plan their investment strategy.</p>
     */
    public function calculateSIP(Request $request)
    {
        $request->validate([
            'monthly_investment' => 'required|numeric|min:0',
            'annual_return_rate' => 'required|numeric|min:0',
            'years' => 'required|integer|min:1'
        ]);

        $P = $request->input('monthly_investment');
        $r = $request->input('annual_return_rate') / 100 / 12;
        $n = $request->input('years') * 12;

        $maturityAmount = $P * ((pow(1 + $r, $n) - 1) / $r) * (1 + $r);

        return response()->json([
            'monthly_investment' => $P,
            'maturity_amount' => round($maturityAmount, 2)
        ]);
    }

    /**
     * Systematic Withdrawal Plan (SWP) Calculation.
     *
     * <p>This API calculates the withdrawal schedule and remaining balance for a Systematic Withdrawal Plan. It helps users plan regular withdrawals while keeping their investments in mutual funds.</p>
     */
    public function calculateSWP(Request $request)
    {
        $request->validate([
            'initial_investment' => 'required|numeric|min:0',
            'monthly_withdrawal' => 'required|numeric|min:0',
            'annual_return_rate' => 'required|numeric|min:0'
        ]);

        $investment = $request->input('initial_investment');
        $withdrawal = $request->input('monthly_withdrawal');
        $r = $request->input('annual_return_rate') / 100 / 12;
        $months = 0;

        while ($investment > 0) {
            $investment = $investment * (1 + $r) - $withdrawal;
            $months++;
            if ($investment <= 0) {
                break;
            }
        }

        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        return response()->json([
            'investment_lasted' => "$years years and $remainingMonths months"
        ]);
    }
}
