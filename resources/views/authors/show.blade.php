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
        <div class="flex items-start gap-6">
            {{-- Author photo --}}
            <div class="w-48 shrink-0">
                @if ($author->photo)
                    <img src="{{ $author->photo }}" alt="{{ $author->name }}" class="w-full rounded-lg shadow">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-500 rounded-lg">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ $author->name }}</h1>

                {{-- Author information + edit/delete buttons --}}
                <div class="flex flex-col mb-6">
                    <div class="mb-4">
                        @if ($author->biography)
                            <p class="text-gray-700 text-lg">{{ $author->biography }}</p>
                        @endif

                        @if ($author->birth_date)
                            <p class="text-gray-600 mt-2">Ngày sinh:
                                {{ \Carbon\Carbon::parse($author->birth_date)->format('d/m/Y') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Edit button --}}
                        <a href="{{ route('admin.authors.edit', $author->id) }}"
                            class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200">
                            ✏️ Chỉnh sửa
                        </a>

                        {{-- Delete button --}}
                        @if ($books->count() > 0)
                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded cursor-not-allowed"
                                title="Không thể xoá khi còn sách">
                                🗑️ Xoá
                            </button>
                        @else
                            <button onclick="document.getElementById('delete-author-modal').classList.remove('hidden')"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                                🗑️ Xoá
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Book list --}}
        <h2 class="text-2xl font-semibold mb-4 mt-8">📚 Danh sách sách của tác giả</h2>

        @if ($books->isEmpty())
            <p class="text-gray-500 italic">Chưa có sách nào của tác giả này.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($books as $book)
                    <div class="bg-white border rounded-lg shadow hover:shadow-md overflow-hidden cursor-pointer transition duration-200"
                        onclick="window.location='{{ route('admin.books.show', $book->id) }}'">
                        @if ($book->cover_image)
                            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-500">
                                Không có ảnh
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-orange-600">{{ $book->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($book->description, 100) }}</p>

                            <ul class="text-sm text-gray-700 mt-3 space-y-1">
                                <li><strong>Danh mục:</strong>
                                    @foreach ($book->categories as $category)
                                        {{ $category->name }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </li>
                                <li><strong>Trạng thái:</strong>
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
                                </li>
                                <li><strong>NXB:</strong> {{ $book->publisher->name ?? 'Không rõ' }}</li>
                                <li><strong>Ngày xuất bản:</strong>
                                    {{ $book->published_at ? \Carbon\Carbon::parse($book->published_at)->format('d/m/Y') : 'Không rõ' }}
                                </li>
                                <li><strong>Số trang:</strong> {{ $book->page_count ?? 'Không rõ' }}</li>
                                <li><strong>Giá:</strong> {{ number_format($book->price, 0, ',', '.') }}₫</li>
                                <li><strong>Tồn kho:</strong> {{ $book->stock }}</li>
                            </ul>

                            <a href="{{ route('admin.books.show', $book->id) }}"
                                class="block text-sm text-blue-600 mt-4 hover:underline">🔍 Xem chi tiết</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $books->links('pagination::tailwind') }}
            </div>
        @endif

        <x-confirm-modal id="delete-author-modal" title="Xác nhận xoá tác giả"
            message="Bạn có chắc chắn muốn xoá tác giả này? Thao tác này không thể hoàn tác!" confirmText="Xoá"
            cancelText="Huỷ" formId="delete-author-form" action="delete" />

        <form id="delete-author-form" action="{{ route('admin.authors.destroy', $author->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
