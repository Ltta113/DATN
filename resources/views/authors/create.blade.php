@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Thêm Tác Giả Mới</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tên tác giả -->
            <div class="mb-6">
                <label for="name" class="block text-left text-gray-700 mb-2">
                    Tên tác giả <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tên tác giả">
                </div>
            </div>

            <!-- Ngày sinh -->
            <div class="mb-6">
                <label for="birth_date" class="block text-left text-gray-700 mb-2">Ngày sinh</label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none">
                </div>
            </div>

            <!-- Hình ảnh tác giả -->
            <div class="mb-6">
                <label for="photo" class="block text-left text-gray-700 mb-2">
                    Hình ảnh tác giả
                </label>

                <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg my-8">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-full h-64 mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                            <img id="imagePreview" src="{{ '/api/placeholder/200/300' }}" alt="Hình ảnh tác giả"
                                class="h-full object-contain">
                        </div>

                        <div class="bg-gray-100 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="bg-transparent w-full py-3 px-4 text-left outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                                onchange="previewImage(event)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tiểu sử -->
            <div class="mb-6">
                <label for="biography" class="block text-left text-gray-700 mb-2">Tiểu sử</label>
                <div class="bg-gray-100 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-gray-500 mx-3 mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <textarea id="biography" name="biography" rows="6"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập tiểu sử tác giả">{{ old('biography') }}</textarea>
                </div>
            </div>

            <!-- Nút submit -->
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg mt-4 transition duration-200 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Lưu Tác Giả
            </button>
        </form>
    </div>

    <script>
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
