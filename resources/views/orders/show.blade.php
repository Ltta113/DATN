@extends('layout')

@section('content')
    <!-- Thêm CSS và JS của Lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết đơn hàng #{{ $order->order_code }}</h1>
            <a href="{{ route('admin.orders.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg transition duration-200">
                Quay lại danh sách
            </a>
        </div>

        <!-- Thông tin người dùng -->
        <div
            class="bg-white shadow-md rounded-lg p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">

            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <!-- Avatar -->
                @if ($order->user && $order->user->avatar)
                    <img src="{{ $order->user->avatar }}" alt="{{ $order->user->full_name }}"
                        class="h-16 w-16 object-cover rounded-full">
                @else
                    <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded-full">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @endif

                <!-- Thông tin người dùng -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $order->name }}</h2>
                    <p class="text-gray-600">{{ $order->email }}</p>
                    <p class="text-gray-600">{{ $order->phone }}</p>
                </div>
            </div>

            <!-- Bên phải: Địa chỉ -->
            <div class="text-left md:text-right max-w-xs">
                <h3 class="text-md font-medium text-gray-700 mb-1">Địa chỉ giao hàng</h3>
                <p class="text-sm text-gray-900">{{ $order->address }}</p>
                <p class="text-sm text-gray-500">
                    {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}
                </p>
            </div>
        </div>

        @if (session('success_order'))
            <div
                class="mb-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success_order') }}</span>
            </div>
        @endif
        @if (session('error_order'))
            <div
                class="mb-6 bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium">{{ session('error_order') }}</span>
            </div>
        @endif

        <!-- Thông tin đơn hàng -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Mã đơn hàng</p>
                    <p class="text-lg font-semibold">#{{ $order->order_code }}</p>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Trạng thái</p>
                    <div class="flex items-center">
                        @switch($order->status)
                            @case('pending')
                                <span class="inline-block w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                                <span class="font-medium">Chờ xử lý</span>
                            @break

                            @case('paid')
                                <span class="inline-block w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                                <span class="font-medium">Đã thanh toán</span>
                            @break

                            @case('completed')
                                <span class="inline-block w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                                <span class="font-medium">Hoàn thành</span>
                            @break

                            @case('canceled')
                                <span class="inline-block w-3 h-3 bg-red-400 rounded-full mr-2"></span>
                                <span class="font-medium">Đã hủy</span>
                            @break

                            @case('shipped')
                                <span class="inline-block w-3 h-3 bg-purple-400 rounded-full mr-2"></span>
                                <span class="font-medium">Đang giao hàng</span>
                            @break

                            @case('refunded')
                                <span class="inline-block w-3 h-3 bg-orange-400 rounded-full mr-2"></span>
                                <span class="font-medium">Đã hoàn tiền</span>
                            @break

                            @case('need_refund')
                                <span class="inline-block w-3 h-3 bg-red-600 rounded-full mr-2"></span>
                                <span class="font-medium">Yêu cầu hoàn tiền</span>
                            @break

                            @case('out_of_stock')
                                <span class="inline-block w-3 h-3 bg-gray-600 rounded-full mr-2"></span>
                                <span class="font-medium">Hết hàng</span>
                            @break

                            @case('admin_canceled')
                                <span class="inline-block w-3 h-3 bg-gray-800 rounded-full mr-2"></span>
                                <span class="font-medium">Đã hủy bởi Admin</span>
                            @break

                            @case('recieived')
                                <span class="inline-block w-3 h-3 bg-teal-400 rounded-full mr-2"></span>
                                <span class="font-medium">Đã nhận hàng</span>
                            @break

                            @default
                                <span class="inline-block w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                                <span class="font-medium">{{ $order->status }}</span>
                        @endswitch
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Phương thức thanh toán</p>
                    <p class="text-lg font-semibold">
                        @if ($order->payment_method == 'zalopay')
                            ZaloPay
                        @elseif($order->payment_method == 'wallet')
                            Ví điện tử
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </p>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Tổng tiền</p>
                    <p class="text-lg font-semibold text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }} ₫
                    </p>
                </div>
            </div>

            <!-- Ghi chú -->
            @if ($order->note)
                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Ghi chú đơn hàng:</h3>
                    <div class="p-4 bg-gray-50 rounded-lg text-gray-700">
                        {{ $order->note }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Feedback của đơn hàng -->
        @if($order->feedback)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Đánh giá từ khách hàng</h2>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $order->feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-gray-600">{{ $order->feedback->rating }}/5 sao</span>
                </div>
                <div class="text-gray-700">
                    {{ $order->feedback->feedback }}
                </div>
                @if($order->feedback->images && count($order->feedback->images) > 0)
                <div class="grid grid-cols-3 gap-2 mt-4">
                    @foreach($order->feedback->images as $image)
                    <div class="relative aspect-square">
                        <img src="{{ $image['url'] }}" alt="Feedback image"
                            class="h-full w-full object-cover rounded-lg hover:opacity-90 transition-opacity cursor-pointer"
                            onclick="openImageModal('{{ $image['url'] }}')">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Danh sách các sản phẩm -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Các sản phẩm trong đơn hàng</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sản phẩm
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Đơn giá
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Số lượng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thành tiền
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($order->orderItems as $item)
                            <tr class="hover:bg-gray-100 cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-16 w-12 bg-gray-100 rounded flex-shrink-0 mr-4">
                                            @if ($item->orderable)
                                                @if ($item->orderable_type === 'App\\Models\\Book')
                                                    @if ($item->orderable->cover_image)
                                                        <a href="{{ route('admin.books.show', $item->orderable->id) }}">
                                                            <img src="{{ $item->orderable->cover_image }}"
                                                                alt="{{ $item->orderable->title }}"
                                                                class="h-full w-full object-cover rounded">
                                                        </a>
                                                    @else
                                                        <div class="h-full w-full bg-gray-200 rounded flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="h-full w-full bg-gray-200 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="h-full w-full bg-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            @if ($item->orderable)
                                                @if ($item->orderable_type === 'App\\Models\\Book')
                                                    <a href="{{ route('admin.books.show', $item->orderable->id) }}" class="text-sm font-medium text-gray-900 hover:underline">
                                                        {{ $item->orderable->title }}
                                                    </a>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $item->orderable->authors->pluck('name')->join(', ') }}
                                                    </div>
                                                @else
                                                    <a href="{{ route('admin.combos.show', $item->orderable->id) }}" class="text-sm font-medium text-gray-900 hover:underline">
                                                        {{ $item->orderable->name }}
                                                    </a>
                                                    <div class="text-sm text-gray-500">
                                                        Combo gồm {{ $item->orderable->books->count() }} sách
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-sm font-medium text-gray-900">Sản phẩm không có sẵn</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($item->price, 0, ',', '.') }} ₫</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }} ₫
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <!-- Footer với tổng tiền -->
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">
                                Tổng cộng:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-lg font-bold text-red-600">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫</div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end">
            @if (
                ($order->status === 'pending' && $order->payment_method === 'cod') ||
                    ($order->status === 'paid' && $order->payment_method !== 'cod'))
                <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-green-500 cursor-pointer hover:bg-green-600 text-white py-2 px-6 rounded-lg transition duration-200">
                        Xác nhận giao hàng
                    </button>
                </form>
            @endif

            @if ($order->status === 'pending' || $order->status === 'paid')
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 cursor-pointer hover:bg-red-600 text-white py-2 px-6 rounded-lg transition duration-200">
                        Hủy đơn hàng
                    </button>
                </form>
            @endif

            @if ($order->status === 'need_refund')
                <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-blue-500 cursor-pointer hover:bg-blue-600 text-white py-2 px-6 rounded-lg transition duration-200">
                        Hoàn tiền
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal xem ảnh -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center">
        <div class="relative max-w-4xl w-full mx-4">
            <img id="modalImage" src="" alt="Full size image" class="w-full h-auto max-h-[90vh] object-contain">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function openImageModal(imageUrl) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageUrl;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Đóng modal khi click bên ngoài ảnh
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Đóng modal khi nhấn phím ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
@endsection
