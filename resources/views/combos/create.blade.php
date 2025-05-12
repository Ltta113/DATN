@extends('layout')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Thêm Combo Mới</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.combos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tên combo -->
                <div>
                    <label for="name" class="block text-left text-gray-700 mb-2">
                        Tên combo <span class="text-red-500">*</span>
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tên combo">
                    </div>
                </div>

                <div>
                    <label for="price" class="block text-left text-gray-700 mb-2">
                        Giá combo <span class="text-red-500">*</span>
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <input type="number" id="price" name="price" min="1000" step="100"
                            value="{{ old('price', 1000) }}" class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Nhập giá combo">
                    </div>
                </div>

                <!-- Ảnh combo -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-left text-gray-700 mb-2">
                        Ảnh combo <span class="text-red-500">*</span>
                    </label>

                    <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg my-8">
                        <div class="flex flex-col items-center">
                            <!-- Image preview -->
                            <div
                                class="w-full h-64 mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <img id="imagePreview"
                                    src="{{ 'https://res.cloudinary.com/dswj1rtvu/image/upload/v1745050814/BookStore/Books/no_cover_available_bjb33v.png' }}"
                                    alt="Combo" class="h-full object-contain">
                            </div>

                            <!-- File input -->
                            <div class="bg-gray-100 rounded-lg flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <input type="file" id="image" name="image" accept="image/*"
                                    class="bg-transparent w-full py-3 px-4 text-left outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                                    onchange="previewImage(event)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="books" class="block text-left text-gray-700 mb-2">
                    Chọn sách cho combo <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg p-4">
                    <!-- Tìm kiếm -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="book_search"
                                class="w-full bg-white border border-gray-300 rounded-lg py-2 px-4 pl-10 focus:outline-none focus:border-blue-500"
                                placeholder="Tìm kiếm sách...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs để lưu trữ thông tin sách đã chọn -->
                    <div id="selected_books_container"></div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select_all" class="rounded">
                                    </th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tên sách
                                    </th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Giá
                                    </th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tồn kho
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="books_table_body" class="divide-y divide-gray-200">
                                @include('combos._books_table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-left text-gray-700 mb-2">
                    Mô tả
                </label>
                <div class="bg-gray-100 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-gray-500 mx-3 mt-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <textarea id="description" name="description" rows="4"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập mô tả về combo">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Tổng giá sách</h3>
                        <p class="text-2xl font-bold text-gray-900" id="total_books_price">0 ₫</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Giá combo</h3>
                        <p class="text-2xl font-bold text-orange-600" id="combo_price">0 ₫</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-semibold mb-2">Tiết kiệm</h3>
                    <p class="text-2xl font-bold text-green-600" id="savings">0 ₫ (0%)</p>
                </div>
            </div>

            <button type="submit"
                class="w-full cursor-pointer bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg mt-8 transition duration-200 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Lưu Combo
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select_all');
            const bookCheckboxes = document.querySelectorAll('.book-checkbox');
            const priceInput = document.getElementById('price');
            const totalBooksPriceElement = document.getElementById('total_books_price');
            const comboPriceElement = document.getElementById('combo_price');
            const savingsElement = document.getElementById('savings');
            const searchInput = document.getElementById('book_search');
            let searchTimeout;

            // Lưu trữ thông tin sách đã chọn
            let selectedBooks = @json($selectedBooks);

            // Hàm cập nhật giá
            function updatePrices() {
                let totalBooksPrice = 0;
                // Tính tổng giá từ tất cả sách đã chọn
                Object.values(selectedBooks).forEach(price => {
                    totalBooksPrice += parseInt(price);
                });

                const comboPrice = parseInt(priceInput.value) || 0;
                const savings = totalBooksPrice - comboPrice;
                const savingsPercentage = totalBooksPrice > 0 ? (savings / totalBooksPrice * 100).toFixed(1) : 0;

                totalBooksPriceElement.textContent = totalBooksPrice.toLocaleString('vi-VN') + ' ₫';
                comboPriceElement.textContent = comboPrice.toLocaleString('vi-VN') + ' ₫';
                savingsElement.textContent = savings.toLocaleString('vi-VN') + ' ₫ (' + savingsPercentage + '%)';

                if (savings > 0) {
                    savingsElement.classList.remove('text-red-600');
                    savingsElement.classList.add('text-green-600');
                } else {
                    savingsElement.classList.remove('text-green-600');
                    savingsElement.classList.add('text-red-600');
                }
            }

            // Hàm tải lại bảng sách
            function loadBooks(search = '') {
                const url = new URL(window.location.href);
                if (search) {
                    url.searchParams.set('search', search);
                } else {
                    url.searchParams.delete('search');
                }

                fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('books_table_body').innerHTML = html;
                        // Gắn lại các event listeners cho các checkbox mới
                        attachCheckboxListeners();
                        // Khôi phục trạng thái checkbox
                        restoreCheckboxStates();
                    });
            }

            // Hàm gắn event listeners cho các checkbox
            function attachCheckboxListeners() {
                const newCheckboxes = document.querySelectorAll('.book-checkbox');
                newCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const bookId = this.value;
                        const bookPrice = this.dataset.price;

                        // Gửi request đến server để cập nhật session
                        fetch('{{ route('admin.combos.toggle-book') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    book_id: bookId,
                                    book_price: bookPrice,
                                    is_selected: this.checked
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    selectedBooks = data.selected_books;
                                    updatePrices();
                                }
                            });
                    });
                });
            }

            // Hàm khôi phục trạng thái checkbox
            function restoreCheckboxStates() {
                const newCheckboxes = document.querySelectorAll('.book-checkbox');
                newCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectedBooks.hasOwnProperty(checkbox.value);
                });
                updatePrices();
            }

            // Event listener cho tìm kiếm
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadBooks(this.value);
                }, 300);
            });

            // Event listener cho checkbox "Chọn tất cả"
            selectAllCheckbox.addEventListener('change', function() {
                bookCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    const bookId = checkbox.value;
                    const bookPrice = checkbox.dataset.price;

                    // Gửi request đến server để cập nhật session
                    fetch('{{ route('admin.combos.toggle-book') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                book_id: bookId,
                                book_price: bookPrice,
                                is_selected: this.checked
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                selectedBooks = data.selected_books;
                                updatePrices();
                            }
                        });
                });
            });

            // Event listener cho input giá
            priceInput.addEventListener('input', updatePrices);

            // Gắn event listeners ban đầu
            attachCheckboxListeners();
            restoreCheckboxStates();

            // Khởi tạo giá trị ban đầu
            updatePrices();
        });

        function previewImage(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
