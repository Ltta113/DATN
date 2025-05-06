<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Service\ZaloPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate(
            [
                'amount' => 'required|numeric|min:10000|max:1000000',
            ],
            [
                'amount.required' => 'Số tiền không được để trống',
                'amount.numeric' => 'Số tiền không hợp lệ',
                'amount.min' => 'Số tiền tối thiểu là 10.000 VNĐ',
                'amount.max' => 'Số tiền tối đa là 1.000.000 VNĐ',
            ]
        );

        $request->user()->wallet ?? Wallet::create(['user_id' => $request->user()->id, 'balance' => 0]);

        DB::beginTransaction();

        try {
            $zaloPay = new ZaloPayService();
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'description' => 'Nạp tiền vào ví',
                'status' => 'pending',
            ]);

            try {
                $payResponse = $zaloPay->deposit(
                    [$transaction->id],
                    (int) $transaction->amount,
                    $request->user()->id,
                    date('ymd') . '_' . rand(0, 1000000),
                );

                DB::commit();

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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' =>
            $e->getMessage()], 500);
        }
    }

    public function callbackDeposite(Request $request)
    {
        $zaloPay = new ZaloPayService();
        return $zaloPay->callbackDeposite($request);
    }

    public function getTransactions(Request $request)
    {
        $transactions = $request->user()->transactions()->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($transactions, 200);
    }
}
