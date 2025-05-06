@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Sách</h1>
            <a href="{{ route('admin.books.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Thêm Sách Mới
            </a>
        </div>

        <div class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="search" name="search"
                            class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Tìm kiếm sách...">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select id="category_filter" name="category_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả thể loại</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select id="publisher_filter" name="publisher_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả NXB</option>
                        @foreach ($publishers as $publisher)
                            <option value="{{ $publisher->id }}"
                                {{ request('publisher') == $publisher->id ? 'selected' : '' }}>
                                {{ $publisher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select id="status_filter" name="status_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động
                        </option>
                        <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Hết hàng</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Đã xóa</option>
                    </select>
                </div>
                <button id="filter_button"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                    Tìm Kiếm
                </button>
            </div>
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
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hình ảnh
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tiêu đề
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nhà xuất bản
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tồn kho
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($books as $book)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.books.show', $book->id) }}'">

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if ($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                        class="h-16 w-12 object-cover rounded">
                                @else
                                    <div class="h-16 w-12 bg-gray-200 flex items-center justify-center rounded">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900" style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ $book->title }}
                                </div>
                                @if ($book->published_at)
                                    <div class="text-xs text-gray-500">Xuất bản: {{ $book->published_at->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm" style="overflow-wrap: break-word; max-width: 150px;">
                                    @foreach ($book->authors as $author)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                                            {{ $author->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="py-3 px-4">
                                @switch($book->status)
                                    @case('active')
                                        <span class="text-green-600">Đang hoạt động</span>
                                    @break

                                    @case('inactive')
                                        <span class="text-gray-500">Không hoạt động</span>
                                    @break

                                    @case('sold_out')
                                        <span class="text-yellow-600">Hết hàng</span>
                                    @break

                                    @case('deleted')
                                        <span class="text-red-600">Đã xóa</span>
                                    @break

                                    @default
                                        <span class="text-gray-500">Chưa xác định</span>
                                @endswitch
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $originalPrice = $book->price;
                                    $discount = $book->discount;

                                    if ($discount) {
                                        if ($discount->percent) {
                                            $finalPrice = $originalPrice * (1 - $discount->percent / 100);
                                        } elseif ($discount->amount) {
                                            $finalPrice = max(0, $originalPrice - $discount->amount);
                                        } else {
                                            $finalPrice = $originalPrice;
                                        }
                                    } else {
                                        $finalPrice = $originalPrice;
                                    }
                                @endphp

                                @if ($discount)
                                    <div class="text-sm text-red-600 font-semibold">
                                        {{ number_format($finalPrice, 0, ',', '.') }} ₫
                                    </div>
                                    <div class="text-xs text-gray-500 line-through">
                                        {{ number_format($originalPrice, 0, ',', '.') }} ₫
                                    </div>
                                @else
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($originalPrice, 0, ',', '.') }} ₫
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div
                                    class="text-sm {{ $book->stock > 10 ? 'text-green-600' : ($book->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $book->stock }}
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.books.edit', $book->id) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    {{-- <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sách này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form> --}}
                                    <a href="
                                    {{ route('admin.books.show', $book->id) }}
                                     "
                                        class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                        <p>Không tìm thấy sách nào</p>
                                        <a href="{{ route('admin.books.create') }}"
                                            class="mt-3 text-blue-600 hover:underline">Thêm sách mới ngay</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const searchParam = urlParams.get('search');
                const categoryParam = urlParams.get('category');
                const publisherParam = urlParams.get('publisher');
                const statusParam = urlParams.get('status');

                if (searchParam) {
                    document.getElementById('search').value = searchParam;
                }

                if (categoryParam) {
                    document.getElementById('category_filter').value = categoryParam;
                }

                if (publisherParam) {
                    document.getElementById('publisher_filter').value = publisherParam;
                }

                if (statusParam) {
                    document.getElementById('status_filter').value = statusParam;
                }

                document.getElementById('filter_button').addEventListener('click', function() {
                    const searchQuery = document.getElementById('search').value;
                    const categoryId = document.getElementById('category_filter').value;
                    const publisherId = document.getElementById('publisher_filter').value;
                    const statusFilter = document.getElementById('status_filter').value;

                    let url = '{{ route('admin.books.index') }}?';
                    let params = [];

                    if (searchQuery) {
                        params.push(`search=${encodeURIComponent(searchQuery)}`);
                    }

                    if (categoryId) {
                        params.push(`category=${categoryId}`);
                    }

                    if (publisherId) {
                        params.push(`publisher=${publisherId}`);
                    }

                    if (statusFilter) {
                        params.push(`status=${statusFilter}`);
                    }

                    window.location.href = url + params.join('&');
                });

                document.getElementById('search').addEventListener('keyup', function(event) {
                    if (event.key === 'Enter') {
                        document.getElementById('filter_button').click();
                    }
                });
            });
        </script>
    @endsection
