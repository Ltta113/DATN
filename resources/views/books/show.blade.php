@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto">
        @if (session('success'))
            <div
                class="mb-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-6 bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden md:flex p-10">
            <div class="md:w-1/2 bg-gray-100 h-96 md:h-auto">
                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                    class="w-full h-full object-cover object-center">
            </div>

            <div class="md:w-1/2 p-8 flex flex-col justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $book->title }}</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 text-base">
                        <p><span class="font-semibold">📅 Xuất bản:</span>
                            {{ $book->published_at ? $book->published_at->format('d/m/Y') : 'Chưa có' }}
                        </p>

                        <div class="flex flex-col">
                            <p><span class="font-semibold">💵 Giá:</span>
                                @if ($book->discount && $book->discount->isActive())
                                    <span class="line-through text-gray-500">{{ number_format($book->price, 0, ',', '.') }}
                                        VNĐ</span>
                                    <span class="text-red-600 font-semibold">
                                        @if ($book->discount->type === 'percent')
                                            {{ number_format($book->price * (1 - $book->discount->value / 100), 0, ',', '.') }}
                                            VNĐ
                                            <span
                                                class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded ml-2">-{{ $book->discount->value }}%</span>
                                        @else
                                            {{ number_format(max(0, $book->price - $book->discount->value), 0, ',', '.') }}
                                            VNĐ
                                            <span
                                                class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded ml-2">-{{ number_format($book->discount->value, 0, ',', '.') }}
                                                VNĐ</span>
                                        @endif
                                    </span>
                                @elseif ($book->discount && $book->discount->isExpired())
                                    <span class="text-gray-500 font-semibold">
                                        {{ number_format($book->price, 0, ',', '.') }} VNĐ
                                        <span
                                            class="bg-gray-100 text-gray-700 text-nowrap text-xs px-2 py-1 rounded ml-2">Khuyến
                                            mãi đã
                                            hết hạn</span>
                                    </span>
                                @else
                                    {{ number_format($book->price, 0, ',', '.') }} VNĐ
                                @endif
                            </p>
                            @if ($book->discount)
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">🏷️ Khuyến mãi:</span>
                                    <a href="{{ route('admin.discounts.show', $book->discount->id) }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $book->discount->name }}
                                    </a>
                                    <span class="text-xs">({{ $book->discount->starts_at->format('d/m/Y') }} -
                                        {{ $book->discount->expires_at->format('d/m/Y') }})</span>
                                </p>
                            @endif
                        </div>

                        <p><span class="font-semibold">📦 Số lượng:</span> {{ $book->stock }}</p>
                        <p><span class="font-semibold">🏢 NXB:</span> {{ $book->publisher->name }}</p>
                        <p class="sm:col-span-2"><span class="font-semibold">✍️ Tác giả:</span>
                            @foreach ($book->authors as $author)
                                <span>{{ $author->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                </span>
                            @endforeach
                        </p>
                        <p class="sm:col-span-2"><span class="font-semibold">📚 Thể loại:</span>
                            @foreach ($book->categories as $category)
                                <span>{{ $category->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                </span>
                            @endforeach
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-4">
                    <a href="{{ route('admin.books.edit', $book->id) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-base px-5 py-2 rounded-lg transition shadow">
                        ✏️ Sửa
                    </a>

                    <form action="{{ route('admin.books.change-status', $book->id) }}" method="POST"
                        class="w-full sm:w-auto">
                        @csrf
                        <select name="status" id="status" onchange="this.form.submit()"
                            class="appearance-none w-full bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                            <option value="inactive" {{ $book->status === 'inactive' ? 'selected' : '' }}>⚪ Chưa hiển thị
                            </option>
                            <option value="active" {{ $book->status === 'active' ? 'selected' : '' }}>🟢 Đang hiển thị
                            </option>
                            <option value="sold_out" {{ $book->status === 'sold_out' ? 'selected' : '' }}>🟡 Đã bán hết
                            </option>
                            <option value="deleted" {{ $book->status === 'deleted' ? 'selected' : '' }}>🔴 Đã xóa</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Phần quản lý khuyến mãi -->
        <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Quản lý khuyến mãi</h2>
            </div>

            <div class="p-6">
                @if ($book->discount && $book->discount->isActive())
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Khuyến mãi hiện tại:
                                    {{ $book->discount->name }}</h3>
                                <p class="text-gray-600">
                                    @if ($book->discount->type === 'percent')
                                        Giảm {{ $book->discount->value }}%
                                    @else
                                        Giảm {{ number_format($book->discount->value, 0, ',', '.') }} VNĐ
                                    @endif
                                    ({{ $book->discount->starts_at->format('d/m/Y') }} -
                                    {{ $book->discount->expires_at->format('d/m/Y') }})
                                </p>
                            </div>

                            <form id="remove-discount" action="{{ route('admin.books.remove-discount', $book->id) }}"
                                method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này khỏi sách?');">
                                @csrf
                                <button type="button"
                                    onclick="document.getElementById('modal-edit-confirm').classList.remove('hidden')"
                                    class="bg-red-500 hover:bg-red-600 cursor-pointer text-white px-4 py-2 rounded transition">
                                    Xóa khuyến mãi
                                </button>
                            </form>
                            <x-confirm-modal id="modal-edit-confirm" title="Xác nhận"
                                message="Bạn có muốn xóa chương trình khuyến mãi khỏi sách này." confirm-text="Xóa"
                                cancel-text="Hủy" form-id="remove-discount" action="delete" />
                        </div>
                    </div>
                @elseif ($book->discount && $book->discount->isExpired())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Khuyến mãi đã hết hạn:
                                    {{ $book->discount->name }}</h3>
                                <p class="text-gray-600">
                                    @if ($book->discount->type === 'percent')
                                        Giảm {{ $book->discount->value }}%
                                    @else
                                        Giảm {{ number_format($book->discount->value, 0, ',', '.') }} VNĐ
                                    @endif
                                    ({{ $book->discount->starts_at->format('d/m/Y') }} -
                                    {{ $book->discount->expires_at->format('d/m/Y') }})
                                </p>
                            </div>

                            <form id="remove-discount" action="{{ route('admin.books.remove-discount', $book->id) }}"
                                method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này khỏi sách?');">
                                @csrf
                                <button type="button"
                                    onclick="document.getElementById('modal-edit-confirm').classList.remove('hidden')"
                                    class="bg-red-500 hover:bg-red-600 cursor-pointer text-white px-4 py-2 rounded transition">
                                    Xóa khuyến mãi
                                </button>
                            </form>
                            <x-confirm-modal id="modal-edit-confirm" title="Xác nhận"
                                message="Bạn có muốn xóa chương trình khuyến mãi khỏi sách này." confirm-text="Xóa"
                                cancel-text="Hủy" form-id="remove-discount" action="delete" />
                        </div>
                    </div>
                @else
                    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-700">Sách này hiện không có khuyến mãi nào được áp dụng.</p>
                    </div>
                @endif

                <h3 class="text-xl font-medium text-gray-800 mb-4">Danh sách khuyến mãi</h3>

                @if ($availableDiscounts->isEmpty())
                    <p class="text-gray-500 italic">Không có khuyến mãi nào khả dụng.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left">Tên khuyến mãi</th>
                                    <th class="py-3 px-4 border-b text-left">Loại</th>
                                    <th class="py-3 px-4 border-b text-left">Giá trị</th>
                                    <th class="py-3 px-4 border-b text-left">Thời gian</th>
                                    <th class="py-3 px-4 border-b text-center">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($availableDiscounts as $discount)
                                    @php
                                        // Tính giá sau giảm
                                        $discountedPrice =
                                            $discount->type === 'percent'
                                                ? $book->price * (1 - $discount->value / 100)
                                                : max(0, $book->price - $discount->value);
                                    @endphp
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                        <td class="py-3 px-4 border-b">
                                            <a href="{{ route('admin.discounts.show', $discount->id) }}"
                                                class="text-blue-600 hover:underline">
                                                {{ $discount->name }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ $discount->type === 'percent' ? 'Phần trăm' : 'Số tiền cố định' }}
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if ($discount->type === 'percent')
                                                {{ $discount->value }}%
                                            @else
                                                {{ number_format($discount->value, 0, ',', '.') }} VNĐ
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ $discount->starts_at->format('d/m/Y') }} -
                                            {{ $discount->expires_at->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <form action="{{ route('admin.books.apply-discount', $book->id) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="discount_id" value="{{ $discount->id }}">
                                                <button type="submit" @if ($discountedPrice <= 0) disabled @endif
                                                    class="bg-green-500 cursor-pointer hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Áp dụng
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (method_exists($availableDiscounts, 'links'))
                        <div class="mt-4">
                            {{ $availableDiscounts->links('pagination::tailwind') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
