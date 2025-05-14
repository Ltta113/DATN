<!-- components/total-component.blade.php -->
<div class="bg-white rounded-lg p-6 mt-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- Thống kê hàng tồn -->
        <div class="bg-blue-50 rounded-lg p-4 shadow-sm border cursor-pointer border-blue-100"
            onclick="window.location.href = '{{ route('admin.books.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Sách Tồn Kho</p>
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalStockBooks) }}</h3>
                        @if (isset($lastMonthStockBooks) && $lastMonthStockBooks > 0)
                            @php
                                $percentChange =
                                    (($totalStockBooks - $lastMonthStockBooks) / $lastMonthStockBooks) * 100;
                            @endphp
                            <span
                                class="ml-2 {{ $percentChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-medium">
                                {{ $percentChange >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4 shadow-sm border cursor-pointer border-green-100"
            onclick="window.location.href = '{{ route('admin.orders.index', ['status' => 'paid']) }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Đơn hàng đang chờ giao</p>
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalValidOrders) }}</h3>
                        @if (isset($lastMonthValidOrders) && $lastMonthValidOrders > 0)
                            @php
                                $percentChange =
                                    (($totalValidOrders - $lastMonthValidOrders) / $lastMonthValidOrders) * 100;
                            @endphp
                            <span
                                class="ml-2 {{ $percentChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-medium">
                                {{ $percentChange >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tổng số sách -->
        <div class="bg-yellow-50 rounded-lg p-4 shadow-sm border cursor-pointer border-yellow-100"
            onclick="window.location.href = '{{ route('admin.books.index') }}'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 font-medium">Tổng Số Sách</p>
                    <div class="flex items-center">
                        <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalBooks) }}</h3>
                        @if (isset($lastMonthTotalBooks) && $lastMonthTotalBooks > 0)
                            @php
                                $percentChange = (($totalBooks - $lastMonthTotalBooks) / $lastMonthTotalBooks) * 100;
                            @endphp
                            <span
                                class="ml-2 {{ $percentChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-medium">
                                {{ $percentChange >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%
                            </span>
                        @endif
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Doanh thu tháng hiện tại -->
        <div id="revenueCard" data-type="revenue"
            class="card-toggle bg-white rounded-lg p-4 shadow-sm border border-gray-200 cursor-pointer hover:bg-blue-50 transition">
            <p class="text-sm text-gray-500 font-medium">Tháng Này</p>
            <div class="flex items-center mt-2">
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalRevenueThisMonth, 0) }} VNĐ</h3>
                @if ($totalRevenueLastMonth > 0)
                    @php
                        $percentChange =
                            (($totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100;
                    @endphp
                    <span
                        class="ml-2 {{ $percentChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-medium">
                        {{ $percentChange >= 0 ? '+' : '' }}{{ number_format($percentChange, 1) }}%
                    </span>
                @endif
            </div>
            <div class="mt-1 text-xs text-gray-500">So với tháng trước</div>
        </div>

        <!-- Đơn Hàng tháng này -->
        <div id="orderCard" data-type="order"
            class="card-toggle bg-white rounded-lg p-4 shadow-sm border border-gray-200 cursor-pointer hover:bg-blue-50 transition">
            <p class="text-sm text-gray-500 font-medium">Đơn Hàng Tháng Này</p>
            <div class="flex items-center mt-2">
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($totalOkOrdersThisMonth) }}</h3>
            </div>
            <div class="mt-1 text-xs text-gray-500">So với tháng trước</div>
        </div>
    </div>

    <!-- Các biểu đồ -->
    <div id="revenueChartWrapper" class="mt-8 bg-white rounded-lg p-4 shadow-sm border border-gray-200">
        <x-revenue-chart />
    </div>

    <div id="orderChartWrapper" class="mt-8 bg-white rounded-lg p-4 shadow-sm border border-gray-200 hidden">
        <x-order-chart />
    </div>

</div>

<script>
    const revenueCard = document.getElementById('revenueCard');
    const orderCard = document.getElementById('orderCard');

    const revenueChart = document.getElementById('revenueChartWrapper');
    const orderChart = document.getElementById('orderChartWrapper');

    revenueCard.addEventListener('click', () => {
        revenueChart.classList.remove('hidden');
        orderChart.classList.add('hidden');
    });

    orderCard.addEventListener('click', () => {
        revenueChart.classList.add('hidden');
        orderChart.classList.remove('hidden');
    });
</script>
