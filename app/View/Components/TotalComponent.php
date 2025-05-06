<?php

namespace App\View\Components;

use App\Models\Book;
use App\Models\Sale;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TotalComponent extends Component
{
    public $totalStockBooks;
    public $totalSoldBooks;
    public $totalRevenueThisMonth;
    public $totalRevenueLastMonth;
    public $totalRevenueAllTime;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->totalStockBooks = Book::sum('stock');
        $this->totalSoldBooks = Book::sum('sold');
        $this->totalRevenueThisMonth = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        $this->totalRevenueLastMonth = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
        $this->totalRevenueAllTime = Sale::sum('amount');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.total-component');
    }
}
