<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Service\PayOSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:500000',
        ]);

        $request->user()->wallet ?? Wallet::create(['user_id' => $request->user()->id, 'balance' => 0]);

        DB::beginTransaction();

        try {
            $payOS = new PayOSService();
            $transaction = $request->user()->transactions()->create([
                'amount' => $request->amount,
                'description' => 'Nạp tiền vào ví',
                'status' => 'pending',
            ]);

            $payOSOrder = [
                "orderCode" => $transaction->id,
                'amount' => (int) $request->amount,
                'description' => 'Nạp tiền vào ví',
                'returnUrl' => env('FRONTEND_RETURN_URL'),
                'cancelUrl' => env('FRONTEND_RETURN_URL'),
            ];

            try {
                $payResponse = $payOS->createOrder($payOSOrder);

                return response()->json($payResponse, 200);
            } catch (\Throwable $e) {
                $transaction->update([
                    'status' => 'failed',
                ]);
                return response()->json([
                    'message' => 'Nạp tiền thất bại',
                    'error' => $e->getMessage(),
                ], 500);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction deposit error: ' . $e->getMessage());
            return response()->json(['message' =>
            $e->getMessage()], 500);
        }
    }

    public function handlePaymentReturn(Request $request)
    {
        $transaction = $request->user()->transactions()->where('id', $request->order_id)->first();

        DB::beginTransaction();
        try {
            if ($transaction) {
                $transaction->update([
                    'status' => 'completed',
                ]);

                $transaction->user->wallet->increment('balance', $transaction->amount);

                return response()->json([
                    'message' => 'Nạp tiền thành công',
                ], 200);
            } else {
                return response()->json(['message' => '
                    Giao dịch không tồn tại hoặc đã được xử lý trước đó.'], 404);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => '
                Giao dịch không thành công.'], 500);
        }

        return response()->json(['message' => 'Giao dịch không tồn tại hoặc đã được xử lý trước đó.'], 404);
    }
}
