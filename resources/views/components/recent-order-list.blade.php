<!-- components/recent-orders-component.blade.php -->
<div class="col-span-12 xl:col-span-7">
    <!-- ====== Bảng Đơn Hàng Gần Đây Start -->
    <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pt-4 pb-3 sm:px-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Đơn Hàng Gần Đây
                </h3>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}"
                    class="text-theme-sm cursor-pointer shadow-theme-xs inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                    Xem tất cả
                </a>
            </div>
        </div>

        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="min-w-full">
                <!-- table header start -->
                <thead class="border-gray-100 border-y dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap first:pl-0">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Mã đơn hàng
                                </p>
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Khách hàng
                                </p>
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Tổng tiền
                                </p>
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Thời gian
                                </p>
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Phương thức
                                </p>
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Trạng thái
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->

                <!-- table body start -->
                <tbody class="py-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer rounded-md">
                            <td class="px-6 py-3 whitespace-nowrap first:pl-0 rounded-l-2xl">
                                <div class="flex items-center">
                                    <div class="flex items-center pl-2">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $order->order_code }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <p class="text-gray-800 text-theme-sm font-medium dark:text-white/90">
                                        {{ $order->name }}
                                    </p>
                                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                        {{ $order->phone }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <p class="text-gray-800 text-theme-sm font-medium dark:text-white/90">
                                        {{ number_format($order->total_amount, 0) }} VNĐ
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        @switch($order->payment_method)
                                            @case('wallet')
                                                Từ ví điện tử
                                            @break

                                            @case('zalopay')
                                                ZaloPay
                                            @break
                                        @endswitch
                                        </p>

                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap rounded-r-2xl">
                                    <div class="flex items-center">
                                        @switch($order->status)
                                            @case('pending')
                                                <p
                                                    class="bg-yellow-50 text-theme-xs text-yellow-600 dark:bg-yellow-500/15 dark:text-yellow-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đang xử lý
                                                </p>
                                            @break

                                            @case('paid')
                                                <p
                                                    class="bg-blue-50 text-theme-xs text-blue-600 dark:bg-blue-500/15 dark:text-blue-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đã thanh toán
                                                </p>
                                            @break

                                            @case('completed')
                                                <p
                                                    class="bg-success-50 text-theme-xs text-success-600 dark:bg-success-500/15 dark:text-success-500 rounded-full px-2 py-0.5 font-medium">
                                                    Hoàn thành
                                                </p>
                                            @break

                                            @case('refunded')
                                                <p
                                                    class="bg-purple-50 text-theme-xs text-purple-600 dark:bg-purple-500/15 dark:text-purple-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đã hoàn tiền
                                                </p>
                                            @break

                                            @case('canceled')
                                                <p
                                                    class="bg-red-50 text-theme-xs text-red-600 dark:bg-red-500/15 dark:text-red-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đã hủy
                                                </p>
                                            @break

                                            @case('out_of_stock')
                                                <p
                                                    class="bg-red-50 text-theme-xs text-red-600 dark:bg-red-500/15 dark:text-red-500 rounded-full px-2 py-0.5 font-medium">
                                                    Hết hàng
                                                </p>
                                            @break

                                            @case('admin_canceled')
                                                <p
                                                    class="bg-red-50 text-theme-xs text-red-600 dark:bg-red-500/15 dark:text-red-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đã hủy bởi admin
                                                </p>
                                            @break

                                            @case('need_refund')
                                                <p
                                                    class="bg-red-50 text-theme-xs text-red-600 dark:bg-red-500/15 dark:text-red-500 rounded-full px-2 py-0.5 font-medium">
                                                    Cần hoàn tiền
                                                </p>
                                            @break

                                            @case('shipped')
                                                <p
                                                    class="bg-green-50 text-theme-xs text-green-600 dark:bg-green-500/15 dark:text-green-500 rounded-full px-2 py-0.5 font-medium">
                                                    Đang giao hàng
                                                </p>
                                            @break

                                            @default
                                                <p
                                                    class="bg-gray-50 text-theme-xs text-gray-600 dark:bg-gray-500/15 dark:text-gray-500 rounded-full px-2 py-0.5 font-medium">
                                                    {{ $order->status }}
                                                </p>
                                        @endswitch
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Không có đơn hàng nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <!-- table body end -->
                    </table>
                </div>
            </div>
            <!-- ====== Bảng Đơn Hàng Gần Đây End -->
        </div>
