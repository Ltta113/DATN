@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Đơn Hàng</h1>
        </div>

        <div class="mb-6">
            <form action="{{ route('admin.orders.index') }}" method="GET" id="search-form">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="bg-gray-100 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="search" name="search"
                                class="bg-transparent w-full py-3 px-4 text-left outline-none"
                                placeholder="Tìm kiếm đơn hàng...">
                        </div>
                    </div>
                    {{-- Trạng thái --}}
                    <div class="w-full md:w-48">
                        <select name="status" id="status" class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                            </option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành
                            </option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy
                            </option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền
                            </option>
                            <option value="need_refund" {{ request('status') == 'need_refund' ? 'selected' : '' }}>Cần hoàn
                                tiền
                            </option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Hết
                                hàng
                            </option>
                            <option value="admin_canceled" {{ request('status') == 'admin_canceled' ? 'selected' : '' }}>Đã
                                hủy bởi admin
                            </option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đang giao hàng
                            </option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Đã nhận hàng
                            </option>
                        </select>
                    </div>

                    {{-- Phương thức thanh toán --}}
                    <div class="w-full md:w-48">
                        <select name="payment_method" id="payment_method"
                            class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                            <option value="">Tất cả phương thức</option>
                            <option value="zalopay" {{ request('payment_method') == 'ZALOPAY' ? 'selected' : '' }}>ZALOPAY
                            </option>
                            <option value="wallet" {{ request('payment_method') == 'WALLET' ? 'selected' : '' }}>Ví điện tử
                            </option>
                        </select>
                    </div>
                    <input type="hidden" name="sort" id="sort-field" value="{{ request('sort', '') }}">
                    <input type="hidden" name="direction" id="sort-direction" value="{{ request('direction', 'asc') }}">
                    <button type="submit"
                        class="bg-blue-500 cursor-pointer hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                        Tìm Kiếm
                    </button>

                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Mã đơn hàng
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Người đặt
                        </th>
                        <th class="py-3 px-4 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            onclick="sortBy('total_amount')">
                            <div class="flex items-center">
                                Tổng tiền
                                <span class="ml-1">
                                    @if (request('sort') == 'total_amount')
                                        @if (request('direction') == 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4"></path>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Số lượng SP
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Thanh toán
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Địa chỉ
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs text-nowrap font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-gray-100 cursor-pointer"
                            onclick="window.location='{{ route('admin.orders.show', $order->id) }}'">
                            <a href="{{ route('admin.orders.show', $order->id) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->order_code }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->user->full_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-red-600">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $order->orderItems->sum('quantity') }} sản phẩm
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($order->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                @elseif($order->status == 'completed') bg-green-100 text-green-800
                                @elseif($order->status == 'canceled') bg-red-100 text-red-800
                                @elseif($order->status == 'refunded') bg-purple-100 text-purple-800
                                @elseif($order->status == 'need_refund') bg-orange-100 text-orange-800
                                @elseif($order->status == 'out_of_stock') bg-gray-200 text-gray-800
                                @elseif($order->status == 'admin_canceled') bg-gray-300 text-gray-800
                                @elseif($order->status == 'shipped') bg-blue-200 text-blue-800
                                @elseif($order->status == 'received') bg-green-200 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                        @if ($order->status == 'pending')
                                            Chờ xử lý
                                        @elseif($order->status == 'paid')
                                            Đã thanh toán
                                        @elseif($order->status == 'completed')
                                            Hoàn thành
                                        @elseif($order->status == 'canceled')
                                            Đã hủy
                                        @elseif($order->status == 'refunded')
                                            Đã hoàn tiền
                                        @elseif($order->status == 'need_refund')
                                            Cần hoàn tiền
                                        @elseif($order->status == 'admin_canceled')
                                            Đã hủy bởi admin
                                        @elseif($order->status == 'shipped')
                                            Đang giao hàng
                                        @elseif($order->status == 'out_of_stock')
                                            Hết hàng
                                        @elseif($order->status == 'received')
                                            Đã nhận hàng
                                        @else
                                            Không xác định
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if ($order->payment_method == 'cod')
                                            Thanh toán khi nhận hàng
                                        @elseif($order->payment_method == 'zalopay')
                                            ZaloPay
                                        @elseif($order->payment_method == 'wallet')
                                            Ví điện tử
                                        @else
                                            Chưa thanh toán
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $order->address }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->ward }}, {{ $order->district }},
                                        {{ $order->province }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </td>
                            </a>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    <p>Không tìm thấy đơn hàng nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const sortField = urlParams.get('sort');
            const sortDirection = urlParams.get('direction');

            if (searchParam) {
                document.getElementById('search').value = searchParam;
            }

            if (sortField) {
                document.getElementById('sort-field').value = sortField;
            }

            if (sortDirection) {
                document.getElementById('sort-direction').value = sortDirection;
            }

            document.getElementById('search').addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    document.getElementById('search-form').submit();
                }
            });
        });

        function sortBy(field) {
            const currentSort = document.getElementById('sort-field').value;
            const currentDirection = document.getElementById('sort-direction').value;

            let newDirection = 'asc';
            if (currentSort === field && currentDirection === 'asc') {
                newDirection = 'desc';
            }

            document.getElementById('sort-field').value = field;
            document.getElementById('sort-direction').value = newDirection;
            document.getElementById('search-form').submit();
        }
    </script>
@endsection
