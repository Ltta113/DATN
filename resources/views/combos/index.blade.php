@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Combo</h1>
            <a href="{{ route('admin.combos.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Thêm Combo Mới
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
                            class="bg-transparent w-full cursor-pointer py-3 px-4 text-left outline-none"
                            placeholder="Tìm kiếm combo...">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select id="status_filter" name="status_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động
                        </option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Đã xóa</option>
                    </select>
                </div>
                <button id="filter_button"
                    class="bg-blue-500 cursor-pointer hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                    Tìm Kiếm
                </button>
            </div>
        </div>

        @if (session('success_combo'))
            <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-lg">
                {{ session('success_combo') }}
            </div>
        @endif

        @if (session('error_combo'))
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                {{ session('error_combo') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tên Combo
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Số lượng sách
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($combos as $combo)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.combos.show', $combo->id) }}'"
                            onKeyPress="if(event.key === 'Enter') window.location='{{ route('admin.combos.show', $combo->id) }}'"
                            tabindex="0">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900" style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ $combo->name }}
                                </div>
                                @if ($combo->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($combo->description, 100) }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">
                                    {{ $combo->books->count() }} sách
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if ($combo->is_active)
                                    <span class="text-green-600">Đang hoạt động</span>
                                @else
                                    <span class="text-gray-500">Không hoạt động</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($combo->price, 0, ',', '.') }} ₫
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if ($combo->trashed())
                                        <form action="{{ route('admin.combos.restore', $combo->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-green-600 cursor-pointer hover:text-green-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                        {{-- <form action="{{ route('admin.combos.force-delete', $combo->id) }}" method="POST" class="inline" id="force-delete-{{ $combo->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="document.getElementById('modal-force-delete-{{ $combo->id }}').classList.remove('hidden')" class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        <x-confirm-modal id="modal-force-delete-{{ $combo->id }}" title="Xác nhận"
                                            message="Bạn có chắc chắn muốn xóa vĩnh viễn combo này?" confirm-text="Xóa"
                                            cancel-text="Hủy" form-id="force-delete-{{ $combo->id }}" action="delete" /> --}}
                                    @else
                                        <a href="{{ route('admin.combos.edit', $combo->id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.combos.show', $combo->id) }}"
                                        class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="5" class="py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    <p>Không tìm thấy combo nào</p>
                                    <a href="{{ route('admin.combos.create') }}"
                                        class="mt-3 text-blue-600 hover:underline">Thêm combo mới ngay</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $combos->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const statusParam = urlParams.get('status');

            if (searchParam) {
                document.getElementById('search').value = searchParam;
            }

            if (statusParam) {
                document.getElementById('status_filter').value = statusParam;
            }

            document.getElementById('filter_button').addEventListener('click', function() {
                const searchQuery = document.getElementById('search').value;
                const statusFilter = document.getElementById('status_filter').value;

                let url = '{{ route('admin.combos.index') }}?';
                let params = [];

                if (searchQuery) {
                    params.push(`search=${encodeURIComponent(searchQuery)}`);
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
