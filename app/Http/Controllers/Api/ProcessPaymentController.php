<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProcessPaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */

    //  TANPA SELECT PAYMENT METHOD
    // public function __invoke(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'amount' => 'required'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     $transaction = Transaction::create([
    //         'invoice_number' => 'INV-' . uniqid(),
    //         'amount' => $request->amount,
    //         'status' => 'CREATED'
    //     ]);

    //     $serverKey = config('services.midtrans.server_key');
    //     $baseUri = config('services.midtrans.base_uri', 'https://api.sandbox.midtrans.com');

    //     $resp = Http::withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //     ])->withBasicAuth($serverKey, '')
    //         ->withOptions(['verify' => false]) // Nonaktifkan verifikasi SSL
    //         ->post($baseUri . '/v2/charge', [
    //             'payment_type' => 'gopay',
    //             'transaction_details' => [
    //                 'order_id' => $transaction->id,
    //                 'gross_amount' => $transaction->amount
    //             ]
    //         ]);

    //     if ($resp->status() == 201 || $resp->status() == 200) {
    //         $actions = $resp->json('actions');
    //         if (empty($actions)) {
    //             return response()->json(['message' => $resp['status_message']], 500);
    //         }
    //         $actionMap = [];
    //         foreach ($actions as $action) {
    //             $actionMap[$action['name']] = $action['url'];
    //         }

    //         return response()->json(['qr' => $actionMap['generate-qr-code']]);
    //     }
    //     return response()->json(['message' => $resp->body()], 500);
    // }

    // MENGGUNAKAN SELECT PAYMENT METHOD
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|string|in:gopay,bank_transfer'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transaction = Transaction::create([
            'invoice_number' => 'INV-' . uniqid(),
            'amount' => $request->amount,
            'status' => 'CREATED'
        ]);

        $serverKey = config('services.midtrans.server_key');
        $baseUri = config('services.midtrans.base_uri', 'https://api.sandbox.midtrans.com');

        $resp = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->withBasicAuth($serverKey, '')
            ->withOptions(['verify' => false]) // Nonaktifkan verifikasi SSL
            ->post($baseUri . '/v2/charge', [
                // set payment_type berdasarkan request body
                'payment_type' => $request->payment_type,
                'transaction_details' => [
                    'order_id' => $transaction->id,
                    'gross_amount' => $transaction->amount
                ]
            ]);

        // Jika payment_type adalah bank_transfer, tambahkan bank transfer details
        if ($request->payment_type === 'bank_transfer')     {
            $payload['bank_transfer'] = [
                'bank' => $request->bank,
            ];
        }

        if ($resp->status() == 201 || $resp->status() == 200) {
            $actions = $resp->json('actions');
            if (empty($actions)) {
                return response()->json(['message' => $resp['status_message']], 500);
            }
            $actionMap = [];
            foreach ($actions as $action) {
                $actionMap[$action['name']] = $action['url'];
            }

            return response()->json(['qr' => $actionMap['generate-qr-code']]);
        }
        return response()->json(['message' => $resp->body()], 500);
    }
}
