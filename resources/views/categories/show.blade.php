@extends('layout')

@section('content')
    @if (session('success'))
        <div
            class="mb-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
            <svg class="w-6 h-6 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    <div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-2">{{ $discount->name }}</h1>

        {{-- Chi tiết discount + nút chỉnh sửa / xoá --}}
        <div class="flex items-center justify-between mb-6">
            <div class="space-y-2">
                @if ($discount->description)
                    <p class="text-gray-700 text-lg">{{ $discount->description }}</p>
                @endif

                <div class="flex flex-wrap gap-4 text-gray-700">
                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Loại giảm giá:</span>
                        {{ $discount->type === 'percent' ? 'Phần trăm' : 'Số tiền cố định' }}
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Giá trị:</span>
                        @if ($discount->type === 'percent')
                            {{ $discount->value }}%
                        @else
                            {{ number_format($discount->value, 0, ',', '.') }}₫
                        @endif
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Thời gian áp dụng:</span>
                        @if ($discount->starts_at && $discount->expires_at)
                            {{ \Carbon\Carbon::parse($discount->starts_at)->format('d/m/Y H:i') }} -
                            {{ \Carbon\Carbon::parse($discount->expires_at)->format('d/m/Y H:i') }}
                        @elseif($discount->starts_at)
                            Từ {{ \Carbon\Carbon::parse($discount->starts_at)->format('d/m/Y H:i') }}
                        @elseif($discount->expires_at)
                            Đến {{ \Carbon\Carbon::parse($discount->expires_at)->format('d/m/Y H:i') }}
                        @else
                            Không giới hạn
                        @endif
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Trạng thái:</span>
                        @php
                            $now = \Carbon\Carbon::now();
                            $isActive =
                                (!$discount->starts_at || $now >= $discount->starts_at) &&
                                (!$discount->expires_at || $now <= $discount->expires_at);
                        @endphp

                        @if ($isActive)
                            <span class="text-green-600">Đang hoạt động</span>
                        @else
                            <span class="text-red-600">Không hoạt động</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Nút chỉnh sửa --}}
                <a href="{{ route('admin.discounts.edit', $discount->id) }}"
                    class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200">
                    ✏️ Chỉnh sửa
                </a>

                {{-- Nút xoá --}}
                <button onclick="document.getElementById('delete-discount-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                    🗑️ Xoá
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200 mb-6">
            <ul class="flex -mb-px">
                <li class="mr-1">
                    <button onclick="showTab('included-books')" id="included-books-tab"
                        class="inline-block py-3 px-6 text-blue-600 border-b-2 border-blue-600 font-medium">
                        📚 Sách được áp dụng ({{ $includedBooks->total() ?? 0 }})
                    </button>
                </li>
                <li class="mr-1">
                    <button onclick="showTab('excluded-books')" id="excluded-books-tab"
                        class="inline-block py-3 px-6 text-gray-500 hover:text-gray-700 font-medium">
                        📕 Sách chưa áp dụng ({{ $excludedBooks->total() ?? 0 }})
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tab content --}}
        {{-- Danh sách sách đang được áp dụng --}}
        <div id="included-books" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold">Sách đang được áp dụng khuyến mãi</h2>
                <form id="remove-books-form" action="{{ route('admin.discounts.remove-books', $discount->id) }}"
                    method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200 disabled:bg-gray-300 disabled:text-gray-500"
                        id="remove-books-button" disabled>
                        Xóa khỏi khuyến mãi
                    </button>
                </form>
            </div>

            {{-- Search form --}}
            <form action="{{ route('admin.discounts.show', $discount->id) }}" method="GET" class="mb-6">
                <div class="flex gap-2">
                    <input type="hidden" name="tab" value="included">
                    <input type="text" name="included_search" value="{{ request('included_search') }}"
                        placeholder="Tìm theo tên sách..."
                        class="flex-grow px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                        🔍 Tìm kiếm
                    </button>
                    @if (request('included_search'))
                        <a href="{{ route('admin.discounts.show', ['discount' => $discount->id, 'tab' => 'included']) }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-200">
                            ❌ Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </form>

            @if ($includedBooks->isEmpty())
                <p class="text-gray-500 italic">Chưa có sách nào được áp dụng khuyến mãi này.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-10 py-3 px-4 border-b text-left">
                                    <input type="checkbox" id="select-all-included"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="py-3 px-4 border-b text-left">Tên sách</th>
                                <th class="py-3 px-4 border-b text-left">Tác giả</th>
                                <th class="py-3 px-4 border-b text-right">Giá gốc</th>
                                <th class="py-3 px-4 border-b text-right">Giá sau KM</th>
                                <th class="py-3 px-4 border-b text-center">Tồn kho</th>
                                <th class="py-3 px-4 border-b text-center">Trạng thái</th>
                                <th class="py-3 px-4 border-b text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($includedBooks as $book)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">
                                        <input type="checkbox" name="book_ids[]" value="{{ $book->id }}"
                                            form="remove-books-form"
                                            class="included-book-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3 px-4 border-b font-medium">
                                        <a href="{{ route('admin.books.show', $book->id) }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $book->title }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        @foreach ($book->authors as $author)
                                            {{ $author->name }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-4 border-b text-right">
                                        {{ number_format($book->price, 0, ',', '.') }}₫
                                    </td>
                                    <td class="py-3 px-4 border-b text-right font-medium text-red-600">
                                        @php
                                            $discountedPrice =
                                                $discount->type === 'percent'
                                                    ? $book->price * (1 - $discount->value / 100)
                                                    : max(0, $book->price - $discount->value);
                                        @endphp
                                        {{ number_format($discountedPrice, 0, ',', '.') }}₫
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        {{ $book->stock }}
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        @switch($book->status)
                                            @case('active')
                                                <span class="text-green-600">Đang bán</span>
                                            @break

                                            @case('inactive')
                                                <span class="text-gray-500">Không hoạt động</span>
                                            @break

                                            @case('sold_out')
                                                <span class="text-yellow-600">Hết hàng</span>
                                            @break

                                            @default
                                                <span class="text-gray-500">Chưa xác định</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        <a href="{{ route('admin.books.show', $book->id) }}"
                                            class="text-blue-600 hover:underline">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $includedBooks->appends(['tab' => 'included', 'included_search' => request('included_search')])->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        {{-- Danh sách sách chưa được áp dụng --}}
        <div id="excluded-books" class="tab-content hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold">Sách chưa được áp dụng khuyến mãi</h2>
                <form id="add-books-form" action="{{ route('admin.discounts.add-books', $discount->id) }}"
                    method="POST">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition duration-200 disabled:bg-gray-300 disabled:text-gray-500"
                        id="add-books-button" disabled>
                        Thêm vào khuyến mãi
                    </button>
                </form>
            </div>

            {{-- Search form --}}
            <form action="{{ route('admin.discounts.show', $discount->id) }}" method="GET" class="mb-6">
                <div class="flex gap-2">
                    <input type="hidden" name="tab" value="excluded">
                    <input type="text" name="excluded_search" value="{{ request('excluded_search') }}"
                        placeholder="Tìm theo tên sách..."
                        class="flex-grow px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                        🔍 Tìm kiếm
                    </button>
                    @if (request('excluded_search'))
                        <a href="{{ route('admin.discounts.show', ['discount' => $discount->id, 'tab' => 'excluded']) }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-200">
                            ❌ Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </form>

            @if ($excludedBooks->isEmpty())
                <p class="text-gray-500 italic">Không tìm thấy sách phù hợp.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-10 py-3 px-4 border-b text-left">
                                    <input type="checkbox" id="select-all-excluded"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="py-3 px-4 border-b text-left">Tên sách</th>
                                <th class="py-3 px-4 border-b text-left">Tác giả</th>
                                <th class="py-3 px-4 border-b text-right">Giá gốc</th>
                                <th class="py-3 px-4 border-b text-right">Giá sau KM</th>
                                <th class="py-3 px-4 border-b text-center">Tồn kho</th>
                                <th class="py-3 px-4 border-b text-center">Trạng thái</th>
                                <th class="py-3 px-4 border-b text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($excludedBooks as $book)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="py-3 px-4 border-b">
                                        <input type="checkbox" name="book_ids[]" value="{{ $book->id }}"
                                            form="add-books-form"
                                            class="excluded-book-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="py-3 px-4 border-b font-medium">
                                        <a href="{{ route('admin.books.show', $book->id) }}"
                                            class="text-blue-600 hover:underline">
                                            {{ $book->title }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 border-b">
                                        @foreach ($book->authors as $author)
                                            {{ $author->name }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-4 border-b text-right">
                                        {{ number_format($book->price, 0, ',', '.') }}₫
                                    </td>
                                    <td class="py-3 px-4 border-b text-right font-medium text-red-600">
                                        @php
                                            $discountedPrice =
                                                $discount->type === 'percent'
                                                    ? $book->price * (1 - $discount->value / 100)
                                                    : max(0, $book->price - $discount->value);
                                        @endphp
                                        {{ number_format($discountedPrice, 0, ',', '.') }}₫
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        {{ $book->stock }}
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        @switch($book->status)
                                            @case('active')
                                                <span class="text-green-600">Đang bán</span>
                                            @break

                                            @case('inactive')
                                                <span class="text-gray-500">Không hoạt động</span>
                                            @break

                                            @case('sold_out')
                                                <span class="text-yellow-600">Hết hàng</span>
                                            @break

                                            @default
                                                <span class="text-gray-500">Chưa xác định</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3 px-4 border-b text-center">
                                        <a href="{{ route('admin.books.show', $book->id) }}"
                                            class="text-blue-600 hover:underline">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $excludedBooks->appends(['tab' => 'excluded', 'excluded_search' => request('excluded_search')])->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        <x-confirm-modal id="delete-discount-modal" title="Xác nhận xoá chương trình khuyến mãi"
            message="Bạn có chắc chắn muốn xoá chương trình khuyến mãi này? Thao tác này không thể hoàn tác!"
            confirmText="Xoá" cancelText="Huỷ" formId="delete-discount-form" action="delete" />

        <form id="delete-discount-form" action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        // Tab switching
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Show selected tab
            document.getElementById(tabId).classList.remove('hidden');

            // Update tab button styles
            document.querySelectorAll('button[id$="-tab"]').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-gray-500');
            });

            document.getElementById(tabId + '-tab').classList.remove('text-gray-500');
            document.getElementById(tabId + '-tab').classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

            // Update URL with tab parameter
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId === 'included-books' ? 'included' : 'excluded');
            window.history.replaceState({}, '', url);
        }

        // Set initial tab based on URL or default to first tab
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');

            if (tab === 'excluded') {
                showTab('excluded-books');
            } else {
                showTab('included-books');
            }

            // Select all functionality for included books
            document.getElementById('select-all-included').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.included-book-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateRemoveButtonState();
            });

            // Select all functionality for excluded books
            document.getElementById('select-all-excluded').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.excluded-book-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateAddButtonState();
            });

            // Individual checkbox change handlers
            document.querySelectorAll('.included-book-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateRemoveButtonState);
            });

            document.querySelectorAll('.excluded-book-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateAddButtonState);
            });

            // Update button states
            function updateRemoveButtonState() {
                const anyChecked = Array.from(document.querySelectorAll('.included-book-checkbox')).some(cb => cb
                    .checked);
                document.getElementById('remove-books-button').disabled = !anyChecked;
            }

            function updateAddButtonState() {
                const anyChecked = Array.from(document.querySelectorAll('.excluded-book-checkbox')).some(cb => cb
                    .checked);
                document.getElementById('add-books-button').disabled = !anyChecked;
            }
        });
    </script>
@endsection
