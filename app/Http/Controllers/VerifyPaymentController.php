<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'YOUR_SERVER_KEY');

        Log::info('incoming-notification', $request->all());
        if ($signature != $request->signature_key) {
            return response()->json(['message' => 'invalid signature'], 400);
        }

        // mencari transactionnya
        $transaction = Transaction::find($request->order_id);
        if ($transaction) {
            $status = 'PENDING';
            if ($request->transaction_status == 'settlement') {
                $status = 'PAID';
            } else if ($request->transaction_status == 'expired') {
                $status = 'EXPIRED';
            }

            $transaction->status = $status;
            $transaction->save();
        }
        return response()->json(['message' => 'success']);
    }
}
