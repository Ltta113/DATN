@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold">Quản Lý Người dùng</h1>
        </div>

        <div class="mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET" id="search-form">
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
                                placeholder="Tìm kiếm người dùng...">
                        </div>
                    </div>
                    <input type="hidden" name="sort" id="sort-field" value="{{ request('sort', '') }}">
                    <input type="hidden" name="direction" id="sort-direction" value="{{ request('direction', 'asc') }}">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-200">
                        Tìm Kiếm
                    </button>
                </div>
            </form>
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
                            Avatar
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Họ và tên
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Địa chỉ email
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            onclick="sortBy('orders')">
                            <div class="flex items-center">
                                Đơn hàng
                                <span class="ml-1">
                                    @if (request('sort') == 'orders')
                                        @if (request('direction') == 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4"></path>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            onclick="sortBy('wallet')">
                            <div class="flex items-center">
                                Tiền trong tài khoản
                                <span class="ml-1">
                                    @if (request('sort') == 'wallet')
                                        @if (request('direction') == 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16V4m0 0L3 8m4-4l4 4"></path>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($listUsers as $user)
                        <tr class="hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('admin.users.show', $user->id) }}'">

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->full_name }}"
                                        class="h-16 w-12 object-cover rounded">
                                @else
                                    <div class="h-16 w-12 bg-gray-200 flex items-center justify-center rounded">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900"
                                    style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ $user->full_name }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900"
                                    style="overflow-wrap: break-word; max-width: 300px;">
                                    {{ $user->email }}
                                </div>
                            </td>

                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-900">
                                    {{ $user->orders->count() }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if ($user->wallet && $user->wallet->balance)
                                    <div class="text-sm text-red-600 font-semibold">
                                        {{ number_format($user->wallet->balance, 0, ',', '.') }} ₫
                                    </div>
                                @else
                                    <div class="text-sm text-gray-900">
                                        0 ₫
                                    </div>
                                @endif
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="text-gray-600 hover:text-gray-900" onclick="event.stopPropagation()">
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
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    <p>Không tìm thấy người dùng nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $listUsers->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const sortField = urlParams.get('sort');
            const sortDirection = urlParams.get('direction');

            if (searchParam) {
                document.getElementById('search').value = searchParam;
            }

            if (sortField) {
                document.getElementById('sort-field').value = sortField;
            }

            if (sortDirection) {
                document.getElementById('sort-direction').value = sortDirection;
            }

            document.getElementById('search').addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    document.getElementById('search-form').submit();
                }
            });
        });

        function sortBy(field) {
            const currentSort = document.getElementById('sort-field').value;
            const currentDirection = document.getElementById('sort-direction').value;

            let newDirection = 'asc';
            if (currentSort === field && currentDirection === 'asc') {
                newDirection = 'desc';
            }

            document.getElementById('sort-field').value = field;
            document.getElementById('sort-direction').value = newDirection;
            document.getElementById('search-form').submit();
        }
    </script>
@endsection
