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
                        <p><span class="font-semibold">💵 Giá:</span> {{ number_format($book->price, 0, ',', '.') }} VNĐ</p>
                        <p><span class="font-semibold">📦 Số lượng:</span> {{ $book->stock }}</p>
                        <p><span class="font-semibold">🏢 NXB:</span> {{ $book->publisher->name }}</p>
                        <p class="sm:col-span-2"><span class="font-semibold">✍️ Tác giả:</span>
                            @foreach ($book->book_authors as $author)
                                <span>{{ $author->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                </span>
                            @endforeach
                        </p>
                        <p class="sm:col-span-2"><span class="font-semibold">📚 Thể loại:</span>
                            @foreach ($book->book_categories as $category)
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
    </div>
@endsection
