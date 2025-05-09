@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết combo #{{ $combo->id }}</h1>
            <a href="{{ route('admin.combos.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg transition duration-200">
                Quay lại danh sách
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Thông tin combo -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Tên combo</p>
                    <p class="text-lg font-semibold">{{ $combo->name }}</p>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Giá combo</p>
                    <p class="text-lg font-semibold text-orange-600">{{ number_format($combo->price) }} ₫</p>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Tổng giá sách</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($combo->books->sum('price')) }} ₫</p>
                </div>

                <div class="p-4 border rounded-lg">
                    <p class="text-sm text-gray-500">Tiết kiệm</p>
                    @php
                        $totalBooksPrice = $combo->books->sum('price');
                        $savings = $totalBooksPrice - $combo->price;
                        $savingsPercentage = $totalBooksPrice > 0 ? ($savings / $totalBooksPrice) * 100 : 0;
                    @endphp
                    <p class="text-lg font-semibold text-green-600">
                        {{ number_format($savings) }} ₫ ({{ number_format($savingsPercentage, 1) }}%)
                    </p>
                </div>
            </div>

            <!-- Mô tả -->
            @if ($combo->description)
                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Mô tả combo:</h3>
                    <div class="p-4 bg-gray-50 rounded-lg text-gray-700">
                        {{ $combo->description }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Ảnh combo -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ảnh combo</h2>
            <div class="max-w-md mx-auto">
                <img src="{{ $combo->image }}" alt="{{ $combo->name }}" class="w-full h-64 object-contain rounded-lg">
            </div>
        </div>

        <!-- Danh sách sách trong combo -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Danh sách sách trong combo</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sách
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Giá
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tồn kho
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($combo->books as $book)
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-16 w-12 bg-gray-100 rounded flex-shrink-0 mr-4">
                                            @if ($book->cover_image)
                                                <a href="{{ route('admin.books.show', $book->id) }}">
                                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                                        class="h-full w-full object-cover rounded">
                                                </a>
                                            @else
                                                <div class="h-full w-full bg-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.books.show', $book->id) }}" class="text-sm font-medium text-gray-900 hover:underline">
                                                {{ $book->title }}
                                            </a>
                                            <div class="text-sm text-gray-500">
                                                {{ $book->authors->pluck('name')->join(', ') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($book->price) }} ₫</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $book->stock }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('admin.combos.edit', $combo->id) }}"
                class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Chỉnh sửa
            </a>

            <form action="{{ route('admin.combos.destroy', $combo->id) }}" method="POST" class="inline"
                id="delete-combo">
                @csrf
                @method('DELETE')
                <button type="button"
                    onclick="document.getElementById('modal-delete-confirm').classList.remove('hidden')"
                    class="bg-red-500 cursor-pointer hover:bg-red-600 text-white py-2 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa
                </button>
            </form>
            <x-confirm-modal id="modal-delete-confirm" title="Xác nhận"
                message="Bạn có chắc chắn muốn xóa combo này?" confirm-text="Xóa"
                cancel-text="Hủy" form-id="delete-combo" action="delete" />
        </div>
    </div>
@endsection
