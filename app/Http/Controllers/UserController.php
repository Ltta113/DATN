<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp theo số đơn hàng hoặc số dư trong ví
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

        $listUsers = $query->with(['orders', 'wallet'])
            ->paginate(10);

        return view('users.index', compact('listUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, User $user)
    {
        $ordersQuery = $user->orders()->with('orderItems');

        // Lọc theo mã đơn hàng
        if ($request->filled('order_code')) {
            $ordersQuery->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->status);
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $ordersQuery->where('payment_method', $request->payment_method);
        }

        // Sắp xếp theo thời gian tạo đơn hàng, mới nhất lên đầu
        $orders = $ordersQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query()); // Thêm tất cả query parameters vào URL phân trang

        return view('users.show', compact('user', 'orders'));
    }


    /**
     * Hiển thị chi tiết của một đơn hàng cụ thể
     *
     * @param int $userId
     * @param int $orderId
     * @return \Illuminate\View\View
     */
    public function showOrder($userId, $orderId)
    {
        $user = User::findOrFail($userId);
        $order = Order::with(['orderItems.books'])
            ->where('user_id', $userId)
            ->findOrFail($orderId);

        return view('admin.users.order-detail', compact('user', 'order'));
    }
}
