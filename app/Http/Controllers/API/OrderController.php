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

class OrderController extends Controller
{
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

        foreach ($validated['order_items'] as $item) {
            $book = Book::findOrFail($item['book_id']);

            if ($book->stock < $item['quantity'] || $item['quantity'] <= 0) {
                $insufficientStock[] = [
                    'book_id' => $item['book_id'],
                ];
            } else {
                $orderItems[] = [
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
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

        DB::beginTransaction();

        try {
            $total = 0;
            $items = [];

            foreach ($validated['order_items'] as $item) {
                $book = Book::findOrFail($item['book_id']);
                $total += $book->price * $item['quantity'];

                $items[] = [
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price' => $book->price,
                ];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            DB::commit();

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'order' => new OrderResource($order),
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đặt hàng thất bại ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getOrder(Request $request): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)->with('orderItems.book')->get();

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
}
