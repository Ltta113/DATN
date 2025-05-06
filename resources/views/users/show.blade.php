@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Đơn hàng của {{ $user->full_name }}</h1>
            <a href="{{ route('admin.users.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg transition duration-200">
                Quay lại danh sách
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6 flex flex-row justify-between items-center">
            <!-- Bên trái: Avatar + Thông tin người dùng -->
            <div class="flex items-center space-x-4">
                <!-- Avatar -->
                @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" class="h-16 w-16 object-cover rounded-full">
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
                    <h2 class="text-xl font-semibold text-gray-800">{{ $user->full_name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>

                    @if ($user->wallet && $user->wallet->balance)
                        <p class="text-red-600 font-semibold mt-1">
                            Số dư: {{ number_format($user->wallet->balance, 0, ',', '.') }} ₫
                        </p>
                    @endif
                </div>
            </div>

            <!-- Bên phải: Địa chỉ -->
            <div class="text-right max-w-xs">
                <h3 class="text-md font-medium text-gray-700 mb-1">Địa chỉ</h3>
                <p class="text-sm text-gray-900 truncate">{{ $user->address }}</p>
                <p class="text-sm text-gray-500 truncate">
                    {{ $user->ward }}, {{ $user->district }}, {{ $user->province }}
                </p>
            </div>
        </div>


        <!-- Tìm kiếm và lọc đơn hàng -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    {{-- Mã đơn hàng --}}
                    <div class="flex-1">
                        <div class="bg-gray-100 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="order_code" id="order_code" value="{{ request('order_code') }}"
                                class="bg-transparent w-full py-3 px-4 text-left outline-none"
                                placeholder="Tìm kiếm mã đơn hàng...">
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
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy
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

                    {{-- Nút lọc --}}
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white cursor-pointer py-2 px-4 rounded-lg transition duration-200">
                        Lọc đơn hàng
                    </button>
                </div>
            </form>

        </div>

        <!-- Danh sách đơn hàng -->
        <x-list-order :orders="$orders" />
    </div>
@endsection
