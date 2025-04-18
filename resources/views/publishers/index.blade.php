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
            <h1 class="text-2xl font-bold">Quản Lý Nhà Xuất Bản</h1>
            <a href="{{ route('admin.publishers.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Thêm Nhà Xuất Bản
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
                            placeholder="Tìm kiếm nhà xuất bản...">
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
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số lượng sách
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($publishers as $publisher)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.publishers.show', $publisher->id) }}'">
                            <td class="py-3 px-4">
                                @if ($publisher->logo)
                                    <img src="{{ $publisher->logo }}" alt="Logo" class="h-10">
                                @else
                                    <div
                                        class="h-10 w-10 flex items-center justify-center bg-gray-200 text-gray-500 rounded">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $publisher->name }}</td>
                            <td class="py-3 px-4 text-sm text-blue-600 underline">
                                <a href="{{ $publisher->website }}" target="_blank">{{ $publisher->website }}</a>
                            </td>

                            <td class="py-3 px-4 text-sm text-gray-600" style="max-width: 300px; word-wrap: break-word;">
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">{{ $publisher->books_count }}</span> sách
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.publishers.edit', $publisher->id) }}"
                                        class="text-blue-600 hover:text-blue-900" title="Sửa">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.publishers.show', $publisher->id) }}"
                                        class="text-gray-600 hover:text-gray-900" title="Xem chi tiết">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                                    <p>Không có nhà xuất bản nào</p>
                                    <a href="{{ route('admin.publishers.create') }}"
                                        class="mt-3 text-blue-600 hover:underline">Thêm ngay</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $publishers->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('filter_button').addEventListener('click', function() {
                const searchQuery = document.getElementById('search').value;
                let url = '{{ route('admin.publishers.index') }}?';

                if (searchQuery) {
                    url += `search=${encodeURIComponent(searchQuery)}`;
                }

                window.location.href = url;
            });

            document.getElementById('search').addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    document.getElementById('filter_button').click();
                }
            });
        });
    </script>
@endsection
