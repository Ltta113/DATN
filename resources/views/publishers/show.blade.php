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
        {{-- Thông tin NXB --}}
        <div class="flex flex-col md:flex-row items-start justify-between mb-6 gap-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ $publisher->name }}</h1>

                {{-- Mô tả --}}
                @if ($publisher->description)
                    <p class="text-gray-700 text-lg">{{ $publisher->description }}</p>
                @else
                    <p class="text-gray-500 italic">Chưa có mô tả cho nhà xuất bản này.</p>
                @endif

                {{-- Website --}}
                @if ($publisher->website)
                    <p class="text-blue-600 text-sm mt-2">
                        🌐 <a href="{{ $publisher->website }}" target="_blank" rel="noopener noreferrer"
                            class="hover:underline">
                            {{ $publisher->website }}
                        </a>
                    </p>

                    {{-- Preview website --}}
                    <div id="website-preview-wrapper" class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Xem trước website:</p>
                        <iframe id="website-preview" src="{{ $publisher->website }}" class="w-full h-64 border rounded-lg"
                            sandbox="allow-scripts allow-same-origin allow-popups allow-forms" loading="lazy">
                        </iframe>
                    </div>
                @endif
            </div>

            {{-- Logo --}}
            <div class="w-32 h-32 border bg-white rounded-lg overflow-hidden shadow shrink-0">
                @if ($publisher->logo)
                    <img src="{{ $publisher->logo }}" alt="{{ $publisher->name }}" class="object-cover w-full h-full">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                        Không có logo
                    </div>
                @endif
            </div>
        </div>


        {{-- Nút chỉnh sửa / xoá --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.publishers.edit', $publisher->id) }}"
                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200">
                ✏️ Chỉnh sửa
            </a>

            @if ($books->count() > 0)
                <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded cursor-not-allowed"
                    title="Không thể xoá khi còn sách">
                    🗑️ Xoá
                </button>
            @else
                <button onclick="document.getElementById('delete-publisher-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                    🗑️ Xoá
                </button>
            @endif
        </div>

        {{-- Danh sách sách --}}
        <h2 class="text-2xl font-semibold mb-4">📚 Danh sách sách do NXB phát hành</h2>

        @if ($books->isEmpty())
            <p class="text-gray-500 italic">Chưa có sách nào từ nhà xuất bản này.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($books as $book)
                    <div class="bg-white border rounded-lg shadow hover:shadow-md overflow-hidden cursor-pointer transition duration-200"
                        onclick="window.location='{{ route('admin.books.show', $book->id) }}'">
                        @if ($book->cover_image)
                            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-500">
                                Không có ảnh
                            </div>
                        @endif

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-orange-600">{{ $book->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($book->description, 100) }}</p>

                            <ul class="text-sm text-gray-700 mt-3 space-y-1">
                                <li><strong>Tác giả:</strong>
                                    @foreach ($book->authors as $author)
                                        {{ $author->name }}@if (!$loop->last)
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

        {{-- Modal xác nhận xoá --}}
        <x-confirm-modal id="delete-publisher-modal" title="Xác nhận xoá nhà xuất bản"
            message="Bạn có chắc chắn muốn xoá nhà xuất bản này? Thao tác này không thể hoàn tác!" confirmText="Xoá"
            cancelText="Huỷ" formId="delete-publisher-form" action="delete" />

        <form id="delete-publisher-form" action="{{ route('admin.publishers.destroy', $publisher->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
