@extends('layout')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Chỉnh Sửa Sách</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data"
            id="edit-category-form">
            @method('PUT')
            <input type="hidden" name="id" value="{{ $book->id }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tiêu đề -->
                <div>
                    <label for="title" class="block text-left text-gray-700 mb-2">
                        Tiêu đề <span class="text-red-500">*</span>
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <input type="text" id="title" name="title" value="{{ $book->title }}" required
                            class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tiêu đề sách">
                    </div>
                </div>

                <div>
                    <label for="published_at" class="block text-left text-gray-700 mb-2">
                        Ngày xuất bản
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <input type="date" id="published_at" name="published_at"
                            value="{{ optional($book->published_at)->format('Y-m-d') }}"
                            class="bg-transparent w-full py-3 px-4 text-left outline-none">
                    </div>
                </div>

                <div>
                    <label for="publisher_id" class="block text-left text-gray-700 mb-2">
                        Nhà xuất bản
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <select id="publisher_id" name="publisher_id"
                            class="bg-transparent w-full py-3 px-4 text-left outline-none">
                            <option value="">-- Chọn nhà xuất bản --</option>
                            @foreach ($publishers as $publisher)
                                <option value="{{ $publisher->id }}"
                                    {{ $book->publisher_id == $publisher->id ? 'selected' : '' }}>
                                    {{ $publisher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="price" class="block text-left text-gray-700 mb-2">
                        Giá
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <input type="number" id="price" name="price" step="0.01" value="{{ $book->price }}"
                            required class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Nhập giá sách">
                    </div>
                </div>

                <div>
                    <label for="stock" class="block text-left text-gray-700 mb-2">
                        Số lượng tồn
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                            </path>
                        </svg>
                        <input type="number" id="stock" name="stock" value="{{ $book->stock }}" required
                            class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Nhập số lượng tồn kho">
                    </div>
                </div>

                <div>
                    <label for="page_count" class="block text-left text-gray-700 mb-2">
                        Số trang
                    </label>
                    <div class="bg-gray-100 rounded-lg flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <input type="number" id="page_count" name="page_count" value="{{ $book->page_count }}"
                            class="bg-transparent w-full py-3 px-4 text-left outline-none"
                            placeholder="Nhập số trang của sách">
                    </div>
                </div>

                <div>
                    <label for="cover_image" class="block text-left text-gray-700 mb-2">
                        Hình ảnh bìa
                    </label>

                    <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg my-8">
                        <label for="cover_image" class="block text-left text-gray-700 mb-2">
                            Hình ảnh bìa
                        </label>

                        <div class="flex flex-col items-center">
                            <!-- Image preview -->
                            <div
                                class="w-full h-64 mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <img id="imagePreview" src="{{ $book->cover_image ?? '/api/placeholder/200/300' }}"
                                    alt="Book cover" class="h-full object-contain">
                            </div>

                            <!-- File input -->
                            <div class="bg-gray-100 rounded-lg flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <input type="file" id="cover_image" name="cover_image" accept="image/*"
                                    class="bg-transparent w-full py-3 px-4 text-left outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                                    onchange="previewImage(event)">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="mt-6">
                <label for="author_search" class="block text-left text-gray-700 mb-2">
                    Tác giả
                </label>
                <div id="author_wrapper"
                    class="bg-gray-100 border border-gray-200 rounded-lg p-2 flex flex-wrap items-start w-full focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-300">
                    <svg class="w-5 h-5 text-gray-500 mx-2 mt-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>

                    <div id="author_tags" class="flex flex-wrap items-center">
                        @php
                            $selectedAuthors = $book->authors->pluck('id')->toArray();
                        @endphp

                        @foreach ($selectedAuthors as $authorId)
                            @php
                                $author = $authors->firstWhere('id', $authorId);
                            @endphp

                            @if ($author)
                                <div class="tag-item flex items-center bg-gray-200 border border-gray-300 rounded-md py-1 px-2 m-1 text-sm"
                                    data-id="{{ $author->id }}">
                                    <span>{{ $author->name }}</span>
                                    <button type="button" class="remove-tag ml-1 text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <input type="hidden" name="author_ids[]" value="{{ $author->id }}">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <input type="text" id="author_search"
                        class="flex-1 bg-transparent min-w-[120px] py-2 px-2 outline-none"
                        placeholder="Tìm và chọn tác giả...">
                </div>

                <div id="author_dropdown"
                    class="hidden max-h-60 overflow-y-auto mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10">
                    <ul class="py-1">
                        @foreach ($authors as $author)
                            <li class="author-option px-3 py-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between"
                                data-id="{{ $author->id }}" data-name="{{ $author->name }}">
                                <span>{{ $author->name }}</span>
                                <svg class="w-5 h-5 text-transparent check-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-6">
                <label for="category_search" class="block text-left text-gray-700 mb-2">
                    Thể loại
                </label>
                <div id="category_wrapper"
                    class="bg-gray-100 border border-gray-200 rounded-lg p-2 flex flex-wrap items-start w-full focus-within:border-blue-400 focus-within:ring-1 focus-within:ring-blue-300">
                    <svg class="w-5 h-5 text-gray-500 mx-2 mt-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>

                    <div id="category_tags" class="flex flex-wrap items-center">
                        @php
                            $selectedCategories = $book->categories->pluck('id')->toArray();
                        @endphp

                        @foreach ($selectedCategories as $categoryId)
                            @php
                                $category = $categories->firstWhere('id', $categoryId);
                            @endphp

                            @if ($category)
                                <div class="tag-item flex items-center bg-gray-200 border border-gray-300 rounded-md py-1 px-2 m-1 text-sm"
                                    data-id="{{ $category->id }}">
                                    <span>{{ $category->name }}</span>
                                    <button type="button" class="remove-tag ml-1 text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <input type="hidden" name="category_ids[]" value="{{ $category->id }}">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <input type="text" id="category_search"
                        class="flex-1 bg-transparent min-w-[120px] py-2 px-2 outline-none"
                        placeholder="Tìm và chọn thể loại...">
                </div>

                <div id="category_dropdown"
                    class="hidden max-h-60 overflow-y-auto mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10">
                    <ul class="py-1">
                        @foreach ($categories as $category)
                            <li class="category-option px-3 py-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between"
                                data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                <span>{{ $category->name }}</span>
                                <svg class="w-5 h-5 text-transparent check-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </li>
                        @endforeach
                    </ul>
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
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập mô tả về sách">{{ $book->description }}</textarea>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('modal-edit-confirm').classList.remove('hidden')"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg mt-4 hover:bg-blue-700">
                💾 Lưu thay đổi
            </button>

            <x-confirm-modal id="modal-edit-confirm" title="Xác nhận cập nhật"
                message="Bạn có chắc muốn cập nhật thông tin sách này?
                    Nếu bạn cập nhật số lượng tồn kho thì vui lòng cập nhật lại trạng thái của sách này.
                "
                confirm-text="Lưu" cancel-text="Hủy" form-id="edit-category-form" action="edit" />
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupMultiSelect('author');

            setupMultiSelect('category');

            function setupMultiSelect(type) {
                const wrapper = document.getElementById(`${type}_wrapper`);
                const input = document.getElementById(`${type}_search`);
                const dropdown = document.getElementById(`${type}_dropdown`);
                const tagsContainer = document.getElementById(`${type}_tags`);
                const options = document.querySelectorAll(`.${type}-option`);

                input.addEventListener('focus', function() {
                    dropdown.classList.remove('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!wrapper.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                input.addEventListener('input', function() {
                    const searchValue = this.value.toLowerCase();

                    options.forEach(option => {
                        const name = option.getAttribute('data-name').toLowerCase();
                        if (name.includes(searchValue)) {
                            option.classList.remove('hidden');
                        } else {
                            option.classList.add('hidden');
                        }
                    });
                });

                options.forEach(option => {
                    option.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');

                        const existingTags = tagsContainer.querySelectorAll('.tag-item');
                        let alreadySelected = false;

                        existingTags.forEach(tag => {
                            if (tag.getAttribute('data-id') === id) {
                                alreadySelected = true;
                            }
                        });

                        if (!alreadySelected) {
                            const tagItem = document.createElement('div');
                            tagItem.className =
                                'tag-item flex items-center bg-gray-200 border border-gray-300 rounded-md py-1 px-2 m-1 text-sm';
                            tagItem.setAttribute('data-id', id);

                            tagItem.innerHTML = `
                        <span>${name}</span>
                        <button type="button" class="remove-tag ml-1 text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <input type="hidden" name="${type}_ids[]" value="${id}">
                    `;

                            tagsContainer.appendChild(tagItem);

                            const removeButton = tagItem.querySelector('.remove-tag');
                            removeButton.addEventListener('click', function() {
                                tagItem.remove();
                                options.forEach(opt => {
                                    if (opt.getAttribute('data-id') === id) {
                                        opt.querySelector('.check-icon').classList
                                            .remove('text-blue-500');
                                        opt.querySelector('.check-icon').classList
                                            .add('text-transparent');
                                    }
                                });
                            });

                            this.querySelector('.check-icon').classList.remove('text-transparent');
                            this.querySelector('.check-icon').classList.add('text-blue-500');
                        }

                        input.value = '';
                        options.forEach(opt => {
                            opt.classList.remove('hidden');
                        });

                        dropdown.classList.add('hidden');
                        input.focus();
                    });
                });

                const existingTags = tagsContainer.querySelectorAll('.tag-item');
                existingTags.forEach(tag => {
                    const id = tag.getAttribute('data-id');

                    options.forEach(opt => {
                        if (opt.getAttribute('data-id') === id) {
                            opt.querySelector('.check-icon').classList.remove('text-transparent');
                            opt.querySelector('.check-icon').classList.add('text-blue-500');
                        }
                    });

                    const removeButton = tag.querySelector('.remove-tag');
                    removeButton.addEventListener('click', function() {
                        tag.remove();
                        options.forEach(opt => {
                            if (opt.getAttribute('data-id') === id) {
                                opt.querySelector('.check-icon').classList.remove(
                                    'text-blue-500');
                                opt.querySelector('.check-icon').classList.add(
                                    'text-transparent');
                            }
                        });
                    });
                });
            }
        });
    </script>

    <script>
        function previewImage(event) {
            const file = event.target.files[0]; // Get the selected file
            const imagePreview = document.getElementById('imagePreview'); // Get the image element

            if (file) {
                const reader = new FileReader(); // Create a FileReader instance

                // When the file is read, set the image preview's src to the file's data URL
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };

                reader.readAsDataURL(file); // Read the file as a data URL
            }
        }
    </script>

@endsection
