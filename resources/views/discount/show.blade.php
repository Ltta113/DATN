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
        <div class="flex items-start gap-6 mb-6">
            @if($discount->banner)
                <div class="w-1/3">
                    <img src="{{ $discount->banner }}" alt="Banner" class="w-full h-48 object-cover rounded-lg shadow-md">
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ $discount->name }}</h1>
                @if ($discount->description)
                    <p class="text-gray-700 text-lg mb-4">{{ $discount->description }}</p>
                @endif

                <div class="flex flex-wrap gap-4 text-gray-700">
                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Loại giảm giá:</span>
                        {{ $discount->type === 'percent' ? 'Phần trăm' : 'Số tiền cố định' }}
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Giá trị:</span>
                        @if ($discount->type === 'percent')
                            {{ $discount->value }}%
                        @else
                            {{ number_format($discount->value, 0, ',', '.') }}₫
                        @endif
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Thời gian áp dụng:</span>
                        @if ($discount->starts_at && $discount->expires_at)
                            {{ \Carbon\Carbon::parse($discount->starts_at)->format('d/m/Y H:i') }} -
                            {{ \Carbon\Carbon::parse($discount->expires_at)->format('d/m/Y H:i') }}
                        @elseif($discount->starts_at)
                            Từ {{ \Carbon\Carbon::parse($discount->starts_at)->format('d/m/Y H:i') }}
                        @elseif($discount->expires_at)
                            Đến {{ \Carbon\Carbon::parse($discount->expires_at)->format('d/m/Y H:i') }}
                        @else
                            Không giới hạn
                        @endif
                    </div>

                    <div class="bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        <span class="font-medium">Trạng thái:</span>
                        @php
                            $now = \Carbon\Carbon::now();
                            $isActive =
                                (!$discount->starts_at || $now >= $discount->starts_at) &&
                                (!$discount->expires_at || $now <= $discount->expires_at);
                        @endphp

                        @if ($isActive)
                            <span class="text-green-600">Đang hoạt động</span>
                        @else
                            <span class="text-red-600">Không hoạt động</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mb-6">
            {{-- Nút chỉnh sửa --}}
            <a href="{{ route('admin.discounts.edit', $discount->id) }}"
                class="px-4 py-2 bg-yellow-500 cursor-pointer text-nowrap text-white rounded hover:bg-yellow-600 transition duration-200">
                ✏️ Chỉnh sửa
            </a>

            {{-- Nút xoá --}}
            <button onclick="document.getElementById('delete-discount-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-red-500 cursor-pointer text-nowrap text-white rounded hover:bg-red-600 transition duration-200">
                🗑️ Xoá
            </button>
        </div>

        {{-- Tabs --}}
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.discounts.show', ['discount' => $discount->id, 'tab' => 'included']) }}"
                        class="{{ request('tab', 'included') === 'included' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Sách đang áp dụng
                    </a>
                    <a href="{{ route('admin.discounts.show', ['discount' => $discount->id, 'tab' => 'excluded']) }}"
                        class="{{ request('tab') === 'excluded' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Sách chưa áp dụng
                    </a>
                </nav>
            </div>
        </div>

        @if ($errors->has('discount_error'))
            <div
                class="mb-6 bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg shadow-md flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium">{{ $errors->first('discount_error') }}</span>
            </div>
        @endif

        {{-- Danh sách sách đang được áp dụng --}}
        <x-book-with-discount :discount="$discount" :bookWithDiscount="$bookWithDiscount" />

        {{-- Danh sách sách chưa được áp dụng --}}
        <x-book-without-discount :discount="$discount" :bookWithoutDiscount="$bookWithoutDiscount" />

        <x-confirm-modal id="delete-discount-modal" title="Xác nhận xoá chương trình khuyến mãi"
            message="Bạn có chắc chắn muốn xoá chương trình khuyến mãi này? Thao tác này không thể hoàn tác!"
            confirmText="Xoá" cancelText="Huỷ" formId="delete-discount-form" action="delete" />

        <form id="delete-discount-form" action="{{ route('admin.discounts.destroy', $discount->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        // Tab switching
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });

            // Show selected tab
            document.getElementById(tabId).classList.remove('hidden');

            // Update tab button styles
            document.querySelectorAll('button[id$="-tab"]').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                btn.classList.add('text-gray-500');
            });

            document.getElementById(tabId + '-tab').classList.remove('text-gray-500');
            document.getElementById(tabId + '-tab').classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

            // Update URL with tab parameter
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId === 'included-books' ? 'included' : 'excluded');
            window.history.replaceState({}, '', url);
        }
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');

            if (tab === 'excluded') {
                showTab('excluded-books');
            } else {
                showTab('included-books');
            }

            // Select all functionality for included books
            document.getElementById('select-all-included').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.included-book-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateRemoveButtonState();
            });

            // Select all functionality for excluded books
            document.getElementById('select-all-excluded').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.excluded-book-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateAddButtonState();
            });

            // Individual checkbox change handlers
            document.querySelectorAll('.included-book-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateRemoveButtonState);
            });

            document.querySelectorAll('.excluded-book-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateAddButtonState);
            });

            // Update button states
            function updateRemoveButtonState() {
                const anyChecked = Array.from(document.querySelectorAll('.included-book-checkbox')).some(cb => cb
                    .checked);
                document.getElementById('remove-books-button').disabled = !anyChecked;
            }

            function updateAddButtonState() {
                const anyChecked = Array.from(document.querySelectorAll('.excluded-book-checkbox')).some(cb => cb
                    .checked);
                document.getElementById('add-books-button').disabled = !anyChecked;
            }
        });
    </script>
@endsection
