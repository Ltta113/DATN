<?php

namespace App\View\Components;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InfomationSection extends Component
{
    public $countUsers;
    public $countBooks;
    public $bookSoldOut;
    public $countOrders;

    public $userChangePercent;
    public $bookChangePercent;
    public $orderChangePercent;

    public function __construct()
    {
        $now = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();

        // Tổng
        $this->countUsers = User::count();
        $this->countBooks = Book::count();
        $this->bookSoldOut = Book::where('stock', 0)->orWhere('status', 'sold_out')
            ->count();
        $this->countOrders = Order::where('status', '!=', 'cancel')->count();

        // Tuần này và tuần trước
        $userThisWeek = User::where('created_at', '>=', $lastWeek)->count();
        $userLastWeek = User::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])->count();
        $this->userChangePercent = $this->calculateChangePercent($userThisWeek, $userLastWeek);

        $bookThisWeek = Book::where('created_at', '>=', $lastWeek)->count();
        $bookLastWeek = Book::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])->count();
        $this->bookChangePercent = $this->calculateChangePercent($bookThisWeek, $bookLastWeek);

        $orderThisWeek = Order::where('created_at', '>=', $lastWeek)->where('status', '!=', 'cancel')->count();
        $orderLastWeek = Order::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])
            ->where('status', '!=', 'cancel')->count();
        $this->orderChangePercent = $this->calculateChangePercent($orderThisWeek, $orderLastWeek);
    }

    private function calculateChangePercent($current, $previous): int
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return intval((($current - $previous) / $previous) * 100);
    }

    public function render(): View|Closure|string
    {
        return view('components.infomation-section');
    }
}
