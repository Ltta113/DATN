<div id="included-books" class="tab-content">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold">Sách đang được áp dụng khuyến mãi</h2>
        <form id="remove-books-form" action="{{ route('admin.discounts.remove-books', $discount->id) }}" method="POST">
            @csrf
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
                class="px-4 py-2 bg-blue-500 text-white rounded cursor-pointer hover:bg-blue-600 transition duration-200">
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

    @if ($bookWithDiscount->isEmpty())
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
                        <th class="py-3 px-4 border-b text-right">Giá sau giảm</th>
                        <th class="py-3 px-4 border-b text-center">Tồn kho</th>
                        <th class="py-3 px-4 border-b text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookWithDiscount as $book)
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookWithDiscount->links('pagination::tailwind') }}
        </div>
    @endif
</div>
