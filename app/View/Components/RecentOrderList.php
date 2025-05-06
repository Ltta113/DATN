<?php

namespace App\View\Components;

use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecentOrderList extends Component
{
    public $recentOrders;
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

        $this->recentOrders = Order::with('user')
            ->whereNotIn('status', $excludedStatuses)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.recent-order-list');
    }
}
