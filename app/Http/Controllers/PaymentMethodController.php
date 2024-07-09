<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function select(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string',
        ]);

        return redirect()->route('payment', [
            'amount' => $request->amount,
            'payment_type' => $request->payment_type
        ]);
    }
}
