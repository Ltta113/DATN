<?php

namespace App\View\Components;

use App\Models\Sale;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Carbon\Carbon;

class RevenueChart extends Component
{
    public $salesDataByMonth;
    public $salesDataByYear;

    public function __construct()
    {
        $this->salesDataByMonth = [];
        $this->salesDataByYear = [];

        $now = Carbon::now();
        $currentYear = $now->year;

        // --- Doanh thu theo tháng ---
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        for ($i = 1; $i <= 12; $i++) {
            $this->salesDataByMonth[] = (float) $monthlySales->get($i, 0);
        }

        // --- Doanh thu theo năm (5 năm gần nhất) ---
        $fiveYearSales = Sale::selectRaw('YEAR(created_at) as year, SUM(amount) as total')
            ->whereBetween('created_at', [$now->copy()->subYears(4)->startOfYear(), $now->endOfYear()])
            ->groupByRaw('YEAR(created_at)')
            ->pluck('total', 'year');

        for ($i = $currentYear - 4; $i <= $currentYear; $i++) {
            $this->salesDataByYear[] = [
                'year' => $i,
                'total' => (float) $fiveYearSales->get($i, 0),
            ];
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.revenue-chart');
    }
}
