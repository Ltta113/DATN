<?php

namespace App\Service;

use App\Models\Book;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZaloPayService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'app_id'    => env('ZALOPAY_APP_ID'),
            'key1'      => env('ZALOPAY_KEY1'),
            'key2'      => env('ZALOPAY_KEY2'),
            'endpoint'  => env('ZALOPAY_ENDPOINT'),
            'callback'  => env('ZALOPAY_CALLBACK'),
            'deposit'   => env('ZALOPAY_DEPOSIT'),
        ];
    }

    public function createOrder(array $items, int $amount, string $userId, string $trans)
    {
        $embeddata = json_encode([
            'redirecturl' => env('FRONTEND_RETURN_URL') . '/order-result'
        ]);

        $order = [
            'app_id'        => $this->config['app_id'],
            'app_time'      => round(microtime(true) * 1000),
            'app_trans_id'  => $trans,
            'app_user'      => $userId,
            'item'          => json_encode($items),
            'embed_data'    => $embeddata,
            'amount'        => $amount,
            'description'   => "SachVN - Thanh toán đơn hàng #$trans",
            'bank_code'     => '',
            'callback_url'  => $this->config['callback'],
        ];

        $data = implode('|', [
            $order['app_id'],
            $order['app_trans_id'],
            $order['app_user'],
            $order['amount'],
            $order['app_time'],
            $order['embed_data'],
            $order['item'],
        ]);

        $order['mac'] = hash_hmac('sha256', $data, $this->config['key1']);

        return $this->callAPI($this->config['endpoint'], $order);
    }

    public function callbackOrder(Request $request)
    {
        $result = [];

        Log::info('ZaloPay callback received', [
            'raw_request' => $request->getContent()
        ]);

        try {
            $postDataJson = $request->getContent();
            $postDataArr = json_decode($postDataJson, true);

            if (!isset($postDataArr['data']) || !isset($postDataArr['mac'])) {
                Log::error('Invalid callback structure', ['postDataArr' => $postDataArr]);
                return response()->json([
                    'return_code' => -2,
                    'return_message' => 'Invalid request data',
                ]);
            }

            $mac = hash_hmac('sha256', $postDataArr['data'], $this->config['key2']);
            Log::info('MAC comparison', [
                'generated_mac' => $mac,
                'received_mac' => $postDataArr['mac'],
            ]);

            if (strcmp($mac, $postDataArr['mac']) !== 0) {
                Log::error('MAC mismatch');
                $result['return_code'] = -1;
                $result['return_message'] = 'MAC mismatch';
            } else {
                $dataJson = json_decode($postDataArr['data'], true);

                $appTransId = $dataJson['app_trans_id'] ?? null;
                if (!$appTransId) {
                    throw new \Exception('Missing app_trans_id');
                }

                // Cập nhật đơn hàng
                Order::where('order_code', $appTransId)->update(['status' => 'paid']);
                Sale::create([
                    'amount' => $dataJson['amount'],
                    'description' => "ZaloPay - Thanh toán đơn hàng #$appTransId",
                ]);

                $result['return_code'] = 1;
                $result['return_message'] = 'success';
            }
        } catch (\Exception $e) {
            $result['return_code'] = 0;
            $result['return_message'] = $e->getMessage();
        }

        return response()->json($result);
    }


    public function deposit(array $items, int $amount, string $userId, string $trans)
    {
        $embeddata = json_encode([
            'redirecturl' => env('FRONTEND_RETURN_URL') . '/wallet',
        ]);

        $order = [
            'app_id'        => $this->config['app_id'],
            'app_time'      => round(microtime(true) * 1000),
            'app_trans_id'  => $trans,
            'app_user'      => $userId,
            'item'          => json_encode($items),
            'embed_data'    => $embeddata,
            'amount'        => $amount,
            'description'   => "SachVN - Nạp tiền vào ví #$trans",
            'bank_code'     => '',
            'callback_url'  => $this->config['deposit'],
        ];

        $data = implode('|', [
            $order['app_id'],
            $order['app_trans_id'],
            $order['app_user'],
            $order['amount'],
            $order['app_time'],
            $order['embed_data'],
            $order['item'],
        ]);

        $order['mac'] = hash_hmac('sha256', $data, $this->config['key1']);

        return $this->callAPI($this->config['endpoint'], $order);
    }

    public function callbackDeposite(Request $request)
    {
        $result = [];

        try {
            $postDataJson = $request->getContent();
            $postDataArr = json_decode($postDataJson, true);

            $mac = hash_hmac('sha256', $postDataArr['data'], $this->config['key2']);

            if (strcmp($mac, $postDataArr['mac']) !== 0) {
                $result['return_code'] = -1;
                $result['return_message'] = 'MAC mismatch';
            } else {
                $dataJson = json_decode($postDataArr['data'], true);

                $itemArray = json_decode($dataJson['item'], true);
                $transactionId = $itemArray[0];

                Transaction::where('id', $transactionId)
                    ->update(['status' => 'completed']);
                Sale::create([
                    'amount' => $dataJson['amount'],
                    'description' => "ZaloPay - Nạp tiền vào ví #$transactionId",
                ]);

                $user = User::find($dataJson['app_user']);
                $user->wallet->increment('balance', $dataJson['amount']);
                $user->wallet->save();

                $result['return_code'] = 1;
                $result['return_message'] = 'success';
            }
        } catch (\Exception $e) {
            $result['return_code'] = 0;
            $result['return_message'] = $e->getMessage();
        }

        return response()->json($result);
    }

    private function callAPI($url, $data)
    {
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result !== false
            ? json_decode($result, true)
            : ['error' => 'Unable to connect to ZaloPay'];
    }
}
