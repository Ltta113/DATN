<?php

namespace App\View\Components;

use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Carbon\Carbon;

class OrderChart extends Component
{
    public $orderByMonth;
    public $orderByYear;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->orderByMonth = [];
        $this->orderByYear = [];

        $now = Carbon::now();
        $currentYear = $now->year;

        $monthlyOrders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereIn('status', ['paid', 'shipped', 'completed', 'received'])
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        for ($i = 1; $i <= 12; $i++) {
            $this->orderByMonth[] = (float) $monthlyOrders->get($i, 0);
        }

        $yearlyOrders = Order::selectRaw('YEAR(created_at) as year, COUNT(*) as total')
            ->whereIn('status', ['paid', 'shipped', 'completed', 'received'])
            ->groupByRaw('YEAR(created_at)')
            ->pluck('total', 'year');

        for ($i = 2020; $i <= $currentYear; $i++) {
            $this->orderByYear[] = (float) $yearlyOrders->get($i, 0);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.order-chart');
    }
}
