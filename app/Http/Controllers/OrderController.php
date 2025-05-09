<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const UNDEFINED_PRODUCT = 'không xác định';

    public function index(Request $request)
    {
        $query = Order::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            $direction = $request->direction ?? 'asc';

            if ($sortField === 'orders') {
                $query->withCount('orders')
                    ->orderBy('orders_count', $direction);
            } elseif ($sortField === 'wallet') {
                $query->leftJoin('wallets', 'users.id', '=', 'wallets.user_id')
                    ->select('users.*', 'wallets.balance')
                    ->orderBy('wallets.balance', $direction);
            }
        }

        // Sắp xếp theo ngày tạo
        if ($request->has('created_at') && !empty($request->created_at)) {
            $createdAt = $request->created_at;
            $query->orderBy('created_at', $createdAt);
        }

        // Sắp xếp theo trạng thái
        if ($request->has('status') && !empty($request->status)) {
            $status = $request->status;
            $query->where('status', $status);
        }

        if ($request->has('sort') && !empty($request->sort)) {
            $sortField = $request->sort;
            $direction = $request->direction ?? 'asc';

            if (in_array($sortField, ['total_amount'])) {
                $query->orderBy($sortField, $direction);
            } elseif ($sortField === 'customer_name') {
                $query->join('users', 'orders.user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction)
                    ->select('orders.*'); // giữ nguyên select để tránh lỗi
            }
        }

        $orders = $query->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.orderable']);
        return view('orders.show', compact('order'));
    }

    public function shipped(Order $order)
    {
        $isCodPending = $order->status === 'pending' && $order->payment_method === 'cod';
        $isPaidOnline = $order->status === 'paid' && $order->payment_method !== 'cod';

        if ($isCodPending || $isPaidOnline) {
            foreach ($order->orderItems as $item) {
                if ($item->orderable_type === 'App\\Models\\Book') {
                    $book = $item->orderable;
                    if (!$book || $book->stock < $item->quantity || $book->status === 'sold_out') {
                        return redirect()->route('admin.orders.show', [
                            'order' => $order->id,
                        ])->with('error_order', 'Sách "' . ($book->title ?? self::UNDEFINED_PRODUCT) . '" không đủ số lượng trong kho.');
                    }
                } elseif ($item->orderable_type === 'App\\Models\\Combo') {
                    $combo = $item->orderable;
                    if (!$combo || !$combo->is_active) {
                        return redirect()->route('admin.orders.show', [
                            'order' => $order->id,
                        ])->with('error_order', 'Combo "' . ($combo->name ?? self::UNDEFINED_PRODUCT) . '" không còn khả dụng.');
                    }

                    if (!$combo->hasEnoughStock($item->quantity)) {
                        return redirect()->route('admin.orders.show', [
                            'order' => $order->id,
                        ])->with('error_order', 'Combo "' . ($combo->name ?? self::UNDEFINED_PRODUCT) . '" không đủ số lượng trong kho.');
                    }

                    foreach ($combo->books as $book) {
                        if ($book->stock < $item->quantity || $book->status === 'sold_out') {
                            return redirect()->route('admin.orders.show', [
                                'order' => $order->id,
                            ])->with('error_order', 'Sách "' . $book->title . '" trong combo không đủ số lượng trong kho.');
                        }
                    }
                }
            }

            $order->update(['status' => 'shipped']);

            foreach ($order->orderItems as $item) {
                if ($item->orderable_type === 'App\\Models\\Book') {
                    $book = $item->orderable;
                    if ($book) {
                        $book->increment('sold', $item->quantity);
                        $book->decrement('stock', $item->quantity);

                        if ($book->stock < 0) {
                            $book->stock = 0;
                            $book->status = 'sold_out';
                        }

                        $book->save();
                    }
                } elseif ($item->orderable_type === 'App\\Models\\Combo') {
                    $combo = $item->orderable;
                    if ($combo && $combo->hasEnoughStock($item->quantity)) {
                        $combo->updateBooksStock($item->quantity);
                    }
                }
            }

            Notification::create([
                'user_id' => $order->user_id,
                'order_code' => $order->order_code,
                'title' => 'Đơn hàng đang được giao',
                'message' => 'Đơn hàng #' . $order->order_code . ' của bạn đang được giao.',
            ]);

            return redirect()->route('admin.orders.show', [
                'order' => $order->id,
            ])->with('success_order', 'Đơn hàng bắt đầu được giao.');
        }

        return redirect()->route('admin.orders.show', [
            'order' => $order->id,
        ])->with('error_order', 'Đơn hàng không thể giao.');
    }

    public function markOutOfStock(Order $order)
    {
        $outOfStockItems = [];

        Sale::create([
            'amount' => -$order->total_amount,
            'description' => 'Đơn hàng #' . $order->order_code . ' đã bị hủy.',
        ]);

        foreach ($order->orderItems as $item) {
            if ($item->orderable_type === 'App\\Models\\Book') {
                $book = $item->orderable;
                if (!$book || $book->stock < $item->quantity || $book->status === 'sold_out') {
                    $outOfStockItems[] = $book->title ?? self::UNDEFINED_PRODUCT;
                }
            } elseif ($item->orderable_type === 'App\\Models\\Combo') {
                $combo = $item->orderable;
                if (!$combo || !$combo->is_active || !$combo->hasEnoughStock($item->quantity)) {
                    $outOfStockItems[] = $combo->name ?? self::UNDEFINED_PRODUCT;
                }
            }
        }

        if (!empty($outOfStockItems)) {
            $order->update(['status' => 'out_of_stock']);

            Transaction::create([
                'user_id' => $order->user_id,
                'amount' => $order->total_amount,
                'description' => 'Đơn hàng #' . $order->order_code . ' đã bị hủy do hết hàng.',
                'status' => 'completed',
            ]);

            $order->user->wallet->increment('balance', $order->total_amount);

            Notification::create([
                'user_id' => $order->user_id,
                'order_code' => $order->order_code,
                'title' => 'Thông báo sản phẩm hết hàng',
                'message' => 'Các sản phẩm sau đã hết hàng: ' . implode(', ', $outOfStockItems) . '. Tiền đã được hoàn lại vào ví của bạn.',
            ]);

            return redirect()->route('admin.orders.show', [
                'order' => $order->id,
            ])->with('error_order', 'Đơn hàng đã được đánh dấu là hết hàng.');
        }

        $order->update(['status' => 'admin_canceled']);

        Transaction::create([
            'user_id' => $order->user_id,
            'amount' => $order->total_amount,
            'description' => 'Đơn hàng #' . $order->order_code . ' đã bị hủy bởi admin. Tiền đã được hoàn lại vào ví của bạn.',
            'status' => 'completed',
        ]);

        $order->user->wallet->increment('balance', $order->total_amount);

        Notification::create([
            'user_id' => $order->user_id,
            'order_code' => $order->order_code,
            'title' => 'Thông báo đơn hàng bị hủy',
            'message' => 'Đơn hàng #' . $order->order_code . ' của bạn đã bị hủy.',
        ]);

        return redirect()->route('admin.orders.show', [
            'order' => $order->id,
        ])->with('success_order', 'Đơn hàng đã được hủy.');
    }

    public function refundOrder(Order $order, Request $request)
    {
        if ($order->status !== 'need_refund') {
            return redirect()->route('admin.orders.show', [
                'order' => $order->id,
            ])->with('error_order', 'Đơn hàng không đủ điều kiện hoàn tiền.');
        }

        $order->update(['status' => 'refunded']);

        $order->orderItems->each(function ($item) {
            if ($item->orderable_type === 'App\\Models\\Book') {
                $book = $item->orderable;
                if ($book) {
                    $book->increment('stock', $item->quantity);
                    $book->decrement('sold', $item->quantity);
                    if ($book->status === 'sold_out' && $book->stock > 0) {
                        $book->status = 'active';
                    }
                    $book->save();
                }
            } elseif ($item->orderable_type === 'App\\Models\\Combo') {
                $combo = $item->orderable;
                if ($combo) {
                    $combo->refundBooks($item->quantity);
                }
            }
        });

        $order->user->wallet->increment('balance', $order->total_amount);
        $order->user->transactions()->create([
            'description' => "Hoàn tiền đơn hàng #{$order->order_code}",
            'amount' => $order->total_amount,
            'status' => 'completed',
        ]);
        Sale::create([
            'amount' => -$order->total_amount,
            'description' => "Hoàn tiền đơn hàng #{$order->order_code}",
        ]);
        Notification::create([
            'user_id' => $order->user_id,
            'order_code' => $order->order_code,
            'title' => 'Hoàn tiền đơn hàng',
            'message' => "Đơn hàng #{$order->order_code} của bạn đã được hoàn tiền.",
        ]);

        return redirect()->route('admin.orders.show', [
            'order' => $order->id,
        ])->with('success_order', 'Đơn hàng đã được hoàn tiền.');
    }
}
