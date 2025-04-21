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

    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Tác Giả</h1>
            <a href="{{ route('admin.authors.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Thêm Tác Giả Mới
            </a>
        </div>

        <div class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search"
                            class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Tìm kiếm tác giả...">
                    </div>
                </div>
                <button id="filter_button"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                    Tìm Kiếm
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ảnh</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiểu sử
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày sinh
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số sách
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($authors as $author)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.authors.show', $author->id) }}'">
                            <td class="py-3 px-4">
                                @if ($author->photo)
                                    <img src="{{ $author->photo }}" alt="Ảnh tác giả"
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-white">
                                        ?</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $author->name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600" style="max-width: 300px;">
                                {{ Str::limit($author->biography, 100) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $author->birth_date }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                <span class="font-medium">{{ $author->books_count }}</span> sách
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.authors.edit', $author->id) }}"
                                        class="text-blue-600 hover:text-blue-900" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.authors.show', $author->id) }}"
                                        class="text-gray-600 hover:text-gray-900" title="Xem chi tiết">
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
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    <p>Không tìm thấy tác giả nào</p>
                                    <a href="{{ route('admin.authors.create') }}"
                                        class="mt-3 text-blue-600 hover:underline">Thêm tác giả mới ngay</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $authors->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchButton = document.getElementById('filter_button');

            // Lấy giá trị tìm kiếm từ URL (nếu có) và điền vào ô tìm kiếm
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            if (searchParam) {
                searchInput.value = searchParam;
            }

            searchButton.addEventListener('click', function() {
                const searchQuery = searchInput.value;
                let url = '{{ route('admin.authors.index') }}?';

                if (searchQuery) {
                    url += `search=${encodeURIComponent(searchQuery)}`;
                }

                window.location.href = url;
            });

            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>
@endsection
