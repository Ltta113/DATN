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
use App\Http\Resources\ComboResource;
use App\Models\Combo;
use App\Models\Notification;
use App\Models\Sale;
use Illuminate\Support\Str;
use App\Service\PayOSService;
use App\Service\ZaloPayService;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Log as FacadesLog;

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
                'order_items.*.orderable_id' => 'required|integer',
                'order_items.*.orderable_type' => 'required|string|in:book,combo',
                'order_items.*.quantity' => 'required|integer|min:1',
            ],
            [
                'order_items.required' => 'Danh sách sản phẩm không được để trống.',
                'order_items.array' => 'Danh sách sản phẩm không hợp lệ.',
                'order_items.*.orderable_id.required' => 'ID sản phẩm không được để trống.',
                'order_items.*.orderable_id.exists' => 'Sản phẩm không tồn tại.',
                'order_items.*.quantity.required' => 'Số lượng không được để trống.',
                'order_items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
            ]
        );

        $insufficientStock = [];
        $orderItems = [];
        $total = 0;

        foreach ($validated['order_items'] as $item) {

            if ($item['orderable_type'] === 'book') {
                $book = Book::findOrFail($item['orderable_id']);

                if ($book->stock < $item['quantity'] || $item['quantity'] <= 0) {
                    $insufficientStock[] = [
                        'book_id' => $book->id,
                    ];
                } else {
                    $total += $book->final_price * $item['quantity'];

                    $orderItems[] = [
                        'id' => $this->generateShortCode(),
                        'orderable_id' => $book->id,
                        'orderable_type' => 'book',
                        'quantity' => $item['quantity'],
                        'image' => $book->cover_image,
                        'price' => number_format($book->final_price, 2, '.', '')
                    ];
                }
            } else {
                $combo = Combo::findOrFail($item['orderable_id']);

                if (!$combo->hasEnoughStock($item['quantity'])) {
                    $insufficientStock[] = [
                        'combo_id' => $combo->id,
                    ];
                } else {
                    $total += $combo->price * $item['quantity'];

                    $orderItems[] = [
                        'id' => $this->generateShortCode(),
                        'orderable_id' => $combo->id,
                        'orderable_type' => 'combo',
                        'quantity' => $item['quantity'],
                        'image' => $combo->image,
                        'price' => number_format($combo->price, 2, '.', '')
                    ];
                }
            }
        }

        if (!empty($insufficientStock)) {
            $bookIds = collect($insufficientStock)->pluck('book_id');
            $books = Book::whereIn('id', $bookIds)->get();
            $comboIds = collect($insufficientStock)->pluck('combo_id');
            $combos = Combo::whereIn('id', $comboIds)->get();
            return response()->json([
                'message' => 'Một số sách không đủ số lượng tồn kho.',
                'data' => [
                    'books' => BookResource::collection($books),
                    'combos' => ComboResource::collection($combos),
                ]
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
                'district' => 'nullable|string|max:50',
                'province' => 'nullable|string|max:50',
                'ward' => 'nullable|string|max:50',
                'order_items' => 'required|array',
                'order_items.*.orderable_id' => 'required|integer',
                'order_items.*.orderable_type' => 'required|string|in:book,combo',
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
                'order_items.*.orderable_id.required' => 'ID sản phẩm không được để trống.',
                'order_items.*.orderable_id.exists' => 'Sản phẩm không tồn tại.',
                'order_items.*.quantity.required' => 'Số lượng không được để trống.',
                'order_items.*.quantity.integer' => 'Số lượng phải là số nguyên.',
                'order_items.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
                'order_items.*.orderable_type.required' => 'Loại sản phẩm không được để trống.',
                'order_items.*.orderable_type.in' => 'Loại sản phẩm không hợp lệ.',
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
                'district' => $validated['district'] ?? "",
                'province' => $validated['province'] ?? "",
                'ward' => $validated['ward'] ?? "",
                'payment_method' => $validated['payment_method'],
                'note' => $validated['note'],
                'order_code' => date('ymd') . '_' . rand(0, 1000000),
            ]);

            foreach ($validated['order_items'] as $item) {
                if ($item['orderable_type'] === 'book') {
                    $book = Book::findOrFail($item['orderable_id']);

                    if ($book->stock < $item['quantity']) {
                        return response()->json([
                            'message' => "Sách {$book->title} không đủ số lượng tồn kho.",
                        ], 422);
                    }


                    $order->orderItems()->create([
                        'quantity' => $item['quantity'],
                        'price' => $book->final_price,
                        'orderable_id' => $book->id,
                        'orderable_type' => 'App\\Models\\Book',
                    ]);
                } else {
                    $combo = Combo::findOrFail($item['orderable_id']);

                    if (!$combo->hasEnoughStock($item['quantity'])) {
                        return response()->json([
                            'message' => "Combo {$combo->name} không đủ số lượng tồn kho.",
                        ], 422);
                    }

                    $order->orderItems()->create([
                        'quantity' => $item['quantity'],
                        'price' => $combo->price,
                        'orderable_id' => $combo->id,
                        'orderable_type' => 'App\\Models\\Combo',
                    ]);
                }
            }

            $totalAmount = 0;
            foreach ($order->orderItems as $orderItem) {
                if ($orderItem->orderable_type === 'App\\Models\\Book') {
                    $totalAmount += $orderItem->price * $orderItem->quantity;
                } else {
                    $totalAmount += $orderItem->price * $orderItem->quantity;
                }
            }
            $order->update(['total_amount' => number_format($totalAmount, 2, '.', '')]);

            DB::commit();

            if ($validated['payment_method'] === 'zalopay') {
                $zaloPay = new ZaloPayService();

                $items = $order->orderItems->map(function ($item) {
                    return [
                        'name' => $item->orderable_type === 'App\\Models\\Book' ? $item->book->title : $item->combo->name,
                        'quantity' => $item->quantity,
                        'price' => (int) $item->price
                    ];
                })->toArray();

                try {
                    $payResponse = $zaloPay->createOrder(
                        $items,
                        (int) $totalAmount,
                        $request->user()->id,
                        $order->order_code
                    );

                    return response()->json([
                        'message' => 'Đặt hàng thành công',
                        'data' => new OrderResource($order),
                        'checkoutUrl' => $payResponse['order_url']
                    ], 201);
                } catch (\Throwable $e) {
                    return response()->json([
                        'message' => 'Đặt hàng thành công nhưng tạo link thanh toán thất bại',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }

            if ($validated['payment_method'] === 'wallet') {
                $user = $request->user();
                if ($user->wallet->balance < $totalAmount) {
                    return response()->json([
                        'message' => 'Số dư trong ví không đủ để thanh toán.',
                    ], 422);
                }

                $user->wallet->decrement('balance', $totalAmount);
                $user->transactions()->create([
                    'description' => "Thanh toán đơn hàng #{$order->order_code}",
                    'amount' => -$totalAmount,
                    'status' => 'completed',
                ]);

                $order->update(['status' => 'paid']);

                return response()->json([
                    'message' => 'Đặt hàng thành công',
                    'data' => new OrderResource($order),
                ], 201);
            }

            return response()->json([
                'message' => 'Đặt hàng thất bại',
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đặt hàng thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function callbackOrder(Request $request): JsonResponse
    {
        $zaloPay = new ZaloPayService();
        return $zaloPay->callbackOrder($request);
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

    public function cancelOrder(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền hủy đơn hàng này.',
            ], 403);
        }

        if (
            $order->status !== 'pending' &&
            !($order->status === 'paid' && $order->payment_method !== 'cod')
        ) {
            return response()->json([
                'message' => 'Đơn hàng không thể hủy.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($order->status === 'paid') {
                $request->user()->wallet->increment('balance', $order->total_amount);
                $request->user()->transactions()->create([
                    'description' => "Hủy đơn hàng #{$order->order_code}",
                    'amount' => $order->total_amount,
                    'status' => 'completed',
                ]);
            }
            $order->update(['status' => 'canceled']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Hủy đơn hàng thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Hủy đơn hàng thành công',
            'data' => new OrderResource($order),
        ]);
    }

    public function receivedOrder(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền xác nhận đơn hàng này.',
            ], 403);
        }

        if ($order->status !== 'shipped') {
            return response()->json([
                'message' => 'Đơn hàng không thể xác nhận.',
            ], 422);
        }

        $order->update(['status' => 'received']);

        return response()->json([
            'message' => 'Xác nhận nhận đơn hàng thành công',
            'data' => new OrderResource($order),
        ]);
    }

    public function completeOrder(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền xác nhận đơn hàng này.',
            ], 403);
        }

        if ($order->status !== 'received') {
            return response()->json([
                'message' => 'Đơn hàng không thể xác nhận.',
            ], 422);
        }

        $order->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Xác nhận hoàn thành đơn hàng thành công',
            'data' => new OrderResource($order),
        ]);
    }

    public function needRefundOrder(Order $order, Request $request): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền yêu cầu hoàn tiền cho đơn hàng này.',
            ], 403);
        }

        if ($order->status !== 'received') {
            return response()->json([
                'message' => 'Đơn hàng không thể yêu cầu hoàn tiền.',
            ], 422);
        }

        $order->update(['status' => 'need_refund']);

        return response()->json([
            'message' => 'Yêu cầu hoàn tiền cho đơn hàng thành công',
            'data' => new OrderResource($order),
        ]);
    }
}
