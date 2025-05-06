@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Mã Giảm Giá</h1>
            <a href="{{ route('admin.discounts.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Thêm Mã Giảm Giá Mới
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
                            class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Tìm kiếm mã giảm giá...">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select id="type_filter" name="type_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả loại</option>
                        <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Phần trăm
                        </option>
                        <option value="amount" {{ request('type') == 'amount' ? 'selected' : '' }}>Số tiền cố định</option>
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select id="status_filter" name="status_filter"
                        class="w-full bg-gray-100 py-3 px-4 rounded-lg outline-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="future" {{ request('status') == 'future' ? 'selected' : '' }}>Sắp diễn ra</option>
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
                            Tên
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Loại
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Giá trị
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thời gian
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($discounts as $discount)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.discounts.show', $discount->id) }}'">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900" style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ $discount->name }}
                                </div>
                                <div class="text-xs text-gray-500" style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ Str::limit($discount->description, 50) }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $discount->type == 'percent' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $discount->type == 'percent' ? 'Phần trăm' : 'Số tiền cố định' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if ($discount->type == 'percent')
                                    <div class="text-sm text-gray-900">{{ $discount->value }}%</div>
                                @else
                                    <div class="text-sm text-gray-900">{{ number_format($discount->value, 0, ',', '.') }} ₫
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-xs text-gray-500">
                                    Bắt đầu: {{ \Carbon\Carbon::parse($discount->starts_at)->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Kết thúc: {{ \Carbon\Carbon::parse($discount->expires_at)->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if ($discount->isActive())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Đang hoạt động
                                    </span>
                                @elseif($discount->isExpired())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Đã hết hạn
                                    </span>
                                @elseif($discount->isFuture())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Sắp diễn ra
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.discounts.edit', $discount->id) }}"
                                        class="text-blue-600 hover:text-blue-900" onclick="event.stopPropagation();">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.discounts.show', $discount->id) }}"
                                        class="text-gray-600 hover:text-gray-900" onclick="event.stopPropagation();">
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
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Không tìm thấy mã giảm giá nào</p>
                                    <a href="{{ route('admin.discounts.create') }}"
                                        class="mt-3 text-blue-600 hover:underline">Thêm mã giảm giá mới ngay</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $discounts->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const typeParam = urlParams.get('type');
            const statusParam = urlParams.get('status');

            if (searchParam) {
                document.getElementById('search').value = searchParam;
            }

            if (typeParam) {
                document.getElementById('type_filter').value = typeParam;
            }

            if (statusParam) {
                document.getElementById('status_filter').value = statusParam;
            }

            document.getElementById('filter_button').addEventListener('click', function() {
                const searchQuery = document.getElementById('search').value;
                const typeFilter = document.getElementById('type_filter').value;
                const statusFilter = document.getElementById('status_filter').value;

                let url = '{{ route('admin.discounts.index') }}?';
                let params = [];

                if (searchQuery) {
                    params.push(`search=${encodeURIComponent(searchQuery)}`);
                }

                if (typeFilter) {
                    params.push(`type=${typeFilter}`);
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
