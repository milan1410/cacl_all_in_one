<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Income Tax Calculation.
     *
     * <p>This API calculates the total income tax payable based on user inputs like annual income, deductions, and tax slab. It provides a detailed breakdown of tax liability.</p>
     */
    public function calculateIncomeTax(Request $request)
    {
        $request->validate([
            'annual_income' => 'required|numeric|min:0',
            'age' => 'required|integer|min:18'
        ]);

        $income = $request->input('annual_income');
        $age = $request->input('age');

        // Example tax slab logic (these can be updated as per current tax laws)
        $tax = 0;
        if ($income <= 250000) {
            $tax = 0;
        } elseif ($income > 250000 && $income <= 500000) {
            $tax = ($income - 250000) * 0.05;
        } elseif ($income > 500000 && $income <= 1000000) {
            $tax = 12500 + ($income - 500000) * 0.2;
        } else {
            $tax = 112500 + ($income - 1000000) * 0.3;
        }

        return response()->json([
            'annual_income' => $income,
            'tax_liability' => round($tax, 2)
        ]);
    }

    /**
     * Capital Gains Tax Calculation.
     *
     * <p>Calculate the capital gains tax on profits from the sale of investments. Users provide data such as purchase and sale prices, type of asset, and holding period to receive an estimate.</p>
     */
    public function calculateCapitalGainsTax(Request $request)
    {
        $request->validate([
            'capital_gain' => 'required|numeric|min:0',
            'holding_period' => 'required|integer|min:0',
            'asset_type' => 'required|string|in:equity,real_estate,other'
        ]);

        $gain = $request->input('capital_gain');
        $holdingPeriod = $request->input('holding_period');
        $assetType = $request->input('asset_type');

        $tax = 0;

        // Example rules for capital gains tax
        if ($assetType === 'equity') {
            if ($holdingPeriod < 12) {
                // Short-term capital gains (STCG) for equity
                $tax = $gain * 0.15; // 15% STCG tax rate
            } else {
                // Long-term capital gains (LTCG) for equity
                $tax = ($gain > 100000) ? ($gain - 100000) * 0.1 : 0; // 10% after exemption of Rs. 1,00,000
            }
        } elseif ($assetType === 'real_estate') {
            if ($holdingPeriod < 24) {
                $tax = $gain * 0.2; // Example STCG for real estate
            } else {
                $tax = $gain * 0.2; // Example LTCG for real estate with indexation
            }
        } else {
            // Other asset types with custom rules
            $tax = $gain * 0.3; // Example flat rate for short-term gains
        }

        return response()->json([
            'capital_gain' => $gain,
            'tax_liability' => round($tax, 2),
            'asset_type' => $assetType,
            'holding_period' => $holdingPeriod
        ]);
    }
}
