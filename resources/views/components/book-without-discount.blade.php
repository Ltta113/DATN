<div id="excluded-books" class="tab-content hidden">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold">Sách chưa được áp dụng khuyến mãi</h2>
        <form id="add-books-form" action="{{ route('admin.discounts.add-books', $discount->id) }}" method="POST">
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

    @if ($bookWithoutDiscount->isEmpty())
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
                        <th class="py-3 px-4 border-b text-right">Giá sau giảm</th>
                        <th class="py-3 px-4 border-b text-center">Tồn kho</th>
                        <th class="py-3 px-4 border-b text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookWithoutDiscount as $book)
                        @php
                            // Tính giá sau giảm
                            $discountedPrice =
                                $discount->type === 'percent'
                                    ? $book->price * (1 - $discount->value / 100)
                                    : max(0, $book->price - $discount->value);
                        @endphp
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="py-3 px-4 border-b">
                                <input type="checkbox" name="book_ids[]" value="{{ $book->id }}"
                                    form="add-books-form"
                                    class="excluded-book-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    @if ($discountedPrice <= 0) disabled @endif>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookWithoutDiscount->appends(['tab' => 'excluded', 'excluded_search' => request('excluded_search')])->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllExcluded = document.getElementById('select-all-excluded');
        const excludedBookCheckboxes = document.querySelectorAll('.excluded-book-checkbox');
        const addBooksButton = document.getElementById('add-books-button');
        const addForceBooksButton = document.getElementById('add-force-books-button');

        selectAllExcluded.addEventListener('change', function() {
            excludedBookCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllExcluded.checked;
            });
            toggleAddBooksButton();
        });

        excludedBookCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleAddBooksButton);
        });

        function toggleAddBooksButton() {
            const anyChecked = Array.from(excludedBookCheckboxes).some(checkbox => checkbox.checked);
            addBooksButton.disabled = !anyChecked;
            addForceBooksButton.disabled = !anyChecked;
        }
    });

    document.getElementById('add-books-form').addEventListener('submit', function(event) {
        const selectedBooks = Array.from(document.querySelectorAll('.excluded-book-checkbox:checked'))
            .map(checkbox => checkbox.value);
        if (selectedBooks.length === 0) {
            event.preventDefault();
            alert('Vui lòng chọn ít nhất một sách để thêm vào khuyến mãi.');
        }
    });

    document.getElementById('add-force-books-form').addEventListener('submit', function(event) {
        const selectedBooks = Array.from(document.querySelectorAll('.excluded-book-checkbox:checked'))
            .map(checkbox => checkbox.value);
        if (selectedBooks.length === 0) {
            event.preventDefault();
            alert('Vui lòng chọn ít nhất một sách để thêm vào khuyến mãi.');
        }
    });
</script>
