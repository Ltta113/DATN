<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class ListUserOrders extends Component
{
    public $topUsers;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $excludedStatuses = [
            'pending',
            'incomplete',
            'refund',
            'canceled',
            'admin_canceled',
            'out_of_stock',
            'failed',
            'need_refund',
        ];

        $this->topUsers = User::whereHas('orders', function ($query) use ($excludedStatuses) {
            $query->whereNotIn('status', $excludedStatuses);
        })
            ->withCount([
                'orders as valid_orders_count' => function ($query) use ($excludedStatuses) {
                    $query->whereNotIn('status', $excludedStatuses);
                }
            ])
            ->with([
                'orders' => function ($query) use ($excludedStatuses) {
                    $query->whereNotIn('status', $excludedStatuses);
                }
            ])
            ->get()
            ->sortByDesc(fn($user) => $user->orders->sum('total_amount'))
            ->take(10);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.list-user-orders');
    }
}
