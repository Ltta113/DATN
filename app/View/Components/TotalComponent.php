<?php

namespace App\View\Components;

use App\Models\Book;
use App\Models\Order;
use App\Models\Sale;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TotalComponent extends Component
{
    public $totalStockBooks;
    public $totalValidOrders;
    public $totalRevenueThisMonth;
    public $totalRevenueLastMonth;
    public $totalOkOrdersThisMonth;
    public $totalOkOrdersLastMonth;
    public $totalBooks;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->totalStockBooks = Book::sum('stock');
        $this->totalValidOrders = Order::where('status', 'paid')->count();
        $this->totalOkOrdersThisMonth = Order::whereIn('status', ['paid', 'shipped', 'completed', 'received'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $this->totalOkOrdersLastMonth = Order::whereIn('status', ['paid', 'shipped', 'completed', 'received'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $this->totalBooks = Book::count();
        $this->totalRevenueThisMonth = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $this->totalRevenueLastMonth = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.total-component');
    }
}
