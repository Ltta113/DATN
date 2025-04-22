<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\BookResource;
use App\Http\Resources\OrderResource;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Service\PayOSService;

class OrderController extends Controller
{
    function generateShortCode($length = 6): string
    {
        return strtoupper(Str::random($length));
    }

    public function checkOrder(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'order_items' => 'required|array',
                'order_items.*.book_id' => 'required|exists:books,id',
                'order_items.*.quantity' => 'required|integer',
            ],
            [
                'order_items.required' => 'Danh sách sản phẩm không được để trống.',
                'order_items.array' => 'Danh sách sản phẩm không hợp lệ.',
                'order_items.*.book_id.required' => 'ID sách không được để trống.',
                'order_items.*.book_id.exists' => 'Sách không tồn tại.',
                'order_items.*.quantity.required' => 'Số lượng không được để trống.',
                'order_items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
            ]
        );

        $insufficientStock = [];
        $orderItems = [];
        $total = 0;

        foreach ($validated['order_items'] as $item) {
            $book = Book::findOrFail($item['book_id']);

            if ($book->stock < $item['quantity'] || $item['quantity'] <= 0) {
                $insufficientStock[] = [
                    'book_id' => $item['book_id'],
                ];
            } else {
                $total += $book->price * $item['quantity'];

                $orderItems[] = [
                    'id' => $this->generateShortCode(),
                    'book_id' => $book->id,
                    'book_name' => $book->title,
                    'book_image' => $book->cover_image,
                    'quantity' => $item['quantity'],
                    'price' => number_format($book->price, 2, '.', '')
                ];
            }
        }

        if (!empty($insufficientStock)) {
            $bookIds = collect($insufficientStock)->pluck('book_id');
            $books = Book::whereIn('id', $bookIds)->get();

            return response()->json([
                'message' => 'Một số sách không đủ số lượng tồn kho.',
                'data' => BookResource::collection($books),
            ], 422);
        }

        $fakeOrder = [
            'id' => 1,
            'user_id' => $request->user()->id,
            'total_amount' => $total,
            'status' => 'pending',
            'name' => null,
            'phone' => null,
            'address' => null,
            'email' => null,
            'payment_method' => null,
            'note' => null,
            'order_items' => $orderItems,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        return response()->json([
            'message' => 'Đặt hàng thành công',
            'order' => $fakeOrder,
        ], 200);
    }

    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'payment_method' => 'required|string|max:50',
                'note' => 'nullable|string|max:500',
                'order_items' => 'required|array',
                'order_items.*.book_id' => 'required|exists:books,id',
                'order_items.*.quantity' => 'required|integer|min:1',
            ],
            [
                'name.required' => 'Tên không được để trống.',
                'name.string' => 'Tên phải là chuỗi.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'phone.required' => 'Số điện thoại không được để trống.',
                'phone.string' => 'Số điện thoại phải là chuỗi.',
                'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
                'address.required' => 'Địa chỉ không được để trống.',
                'address.string' => 'Địa chỉ phải là chuỗi.',
                'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
                'email.required' => 'Email không được để trống.',
                'email.email' => 'Email không hợp lệ.',
                'email.max' => 'Email không được vượt quá 255 ký tự.',
                'payment_method.required' => 'Phương thức thanh toán không được để trống.',
                'payment_method.string' => 'Phương thức thanh toán phải là chuỗi.',
                'payment_method.max' => 'Phương thức thanh toán không được vượt quá 50 ký tự.',
                'note.string' => 'Ghi chú phải là chuỗi.',
                'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
                'order_items.required' => 'Danh sách sản phẩm không được để trống.',
                'order_items.array' => 'Danh sách sản phẩm không hợp lệ.',
                'order_items.*.book_id.required' => 'ID sách không được để trống.',
                'order_items.*.book_id.exists' => 'Sách không tồn tại.',
                'order_items.*.quantity.required' => 'Số lượng không được để trống.',
                'order_items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
                'order_items.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
            ]
        );

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'status' => 'pending',
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'payment_method' => $validated['payment_method'],
                'note' => $validated['note'],
            ]);

            foreach ($validated['order_items'] as $item) {
                $book = Book::findOrFail($item['book_id']);

                if ($book->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Sách {$book->title} không đủ số lượng tồn kho.",
                    ], 422);
                }

                $order->orderItems()->create([
                    'book_id' => $book->id,
                    'quantity' => $item['quantity'],
                    'price' => $book->price,
                ]);

                $book->decrement('stock', $item['quantity']);
                $book->increment('sold', $item['quantity']);
                if ($book->stock === 0)
                    $book->update(['status' => 'sold_out']);
            }

            $totalAmount = 0;
            foreach ($order->orderItems as $orderItem) {
                $totalAmount += $orderItem->book->price * $orderItem->quantity;
            }
            $order->update(['total_amount' => number_format($totalAmount, 2, '.', '')]);

            DB::commit();

            if ($validated['payment_method'] === 'payos') {
                $payOS = new PayOSService();

                $items = $order->orderItems->map(function ($item) {
                    return [
                        'name' => $item->book->title,
                        'quantity' => $item->quantity,
                        'price' => (int) $item->price
                    ];
                })->toArray();

                $payOSOrder = [
                    "orderCode" => $order->id,
                    'amount' => (int) $order->total_amount,
                    'description' => 'Thanh toán đơn hàng #' . $order->id,
                    'returnUrl' => env('FRONTEND_RETURN_URL') . "/payment-success?order_id={$order->id}",
                    'cancelUrl' => env('FRONTEND_RETURN_URL') . "/payment-cancel?order_id={$order->id}",
                    'webhookUrl' => env('PAYOS_WEBHOOK_URL'),
                    'items' => $items
                ];

                try {
                    $payResponse = $payOS->createOrder($payOSOrder);

                    return response()->json([
                        'message' => 'Đặt hàng thành công',
                        'data' => new OrderResource($order),
                        'checkoutUrl' => $payResponse['checkoutUrl']
                    ], 201);
                } catch (\Throwable $e) {
                    return response()->json([
                        'message' => 'Đặt hàng thành công nhưng tạo link thanh toán thất bại',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'data' => new OrderResource($order),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đặt hàng thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payOS = new PayOSService();
        $payload = $request->all();

        if ($payOS->verifyPayment($payload)) {
            $orderId = $payload['orderCode'];
            $status = $payload['status'];

            $order = Order::find($orderId);
            if ($order && $status === 'pending') {
                $order->update(['status' => 'paid']);
            }

            return response()->json(['received' => true]);
        }

        return response()->json(['error' => 'Invalid signature'], 400);
    }


    public function getOrders(Request $request): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => OrderResource::collection($order),
        ]);
    }

    public function getOrderDetail(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền xem chi tiết đơn hàng này.',
            ], 403);
        }

        return response()->json([
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => new OrderResource($order),
        ]);
    }

    public function updateStatus(Request $request)
    {
        $orderCode = $request->input('orderCode');

        $order = Order::find($orderCode);

        if (!$order) {
            return response()->json([
                'message' => '
                Đơn hàng không tồn tại.'
            ], 404);
        }

        $order->status = 'paid';
        $order->save();

        return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công.'], 200);
    }
}
