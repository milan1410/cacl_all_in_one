<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    /**
     * General Calculation.
     *
     * <p>This API endpoint performs a general calculation. The `calculate` method handles the input parameters and provides a result based on the implemented logic. This endpoint is versatile and can be tailored for different types of computational tasks.</p>
     */

    public function calculate(Request $request)
    {
        $num1 = $request->input('num1');
        $num2 = $request->input('num2');
        $operation = $request->input('operation');

        if (!is_numeric($num1) || !is_numeric($num2)) {
            return response()->json(['error' => 'Inputs must be numbers'], 400);
        }

        switch ($operation) {
            case 'add':
                $result = $num1 + $num2;
                break;
            case 'subtract':
                $result = $num1 - $num2;
                break;
            case 'multiply':
                $result = $num1 * $num2;
                break;
            case 'divide':
                if ($num2 == 0) {
                    return response()->json(['error' => 'Division by zero is not allowed'], 400);
                }
                $result = $num1 / $num2;
                break;
            default:
                return response()->json(['error' => 'Invalid operation'], 400);
        }

        return response()->json(['result' => $result]);
    }
}
