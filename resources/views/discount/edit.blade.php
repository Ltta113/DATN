@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Chỉnh Sửa Discount</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.discounts.update', $discount->id) }}" id="edit-publisher-form" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tên Discount -->
            <div class="mb-6">
                <label for="name" class="block text-left text-gray-700 mb-2">
                    Tên Chương trình <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <input type="text" id="name" name="name" value="{{ old('name', $discount->name) }}" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tên discount">
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mb-6">
                <label for="description" class="block text-left text-gray-700 mb-2">Mô tả</label>
                <div class="bg-gray-100 rounded-lg flex items-start">
                    <textarea id="description" name="description" rows="4"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập mô tả discount">{{ old('description', $discount->description) }}</textarea>
                </div>
            </div>

            <!-- Banner -->
            <div class="mb-6">
                <label for="banner" class="block text-left text-gray-700 mb-2">
                    Banner
                </label>

                <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg my-8">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-full h-64 mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                            <img id="imagePreview"
                                src="{{ $discount->banner ?? 'https://res.cloudinary.com/dswj1rtvu/image/upload/v1745051027/BookStore/Discounts/default-discount-banner.jpg' }}"
                                alt="Banner" class="h-full object-contain">
                        </div>

                        <div class="bg-gray-100 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <input type="file" id="banner" name="banner" accept="image/*"
                                class="bg-transparent w-full py-3 px-4 text-left outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                                onchange="previewImage(event)">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</p>
                        @if ($discount->banner)
                            <div class="flex items-center mt-2">
                                <input type="checkbox" id="remove_banner" name="remove_banner" class="mr-2">
                                <label for="remove_banner" class="text-sm text-gray-600">Xóa banner hiện tại</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Kiểu giảm giá -->
            <div class="mb-6">
                <label for="type" class="block text-left text-gray-700 mb-2">
                    Kiểu giảm giá <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <select id="type" name="type" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none">
                        <option value="percent" {{ old('type', $discount->type) === 'percent' ? 'selected' : '' }}>Phần trăm
                        </option>
                        <option value="amount" {{ old('type', $discount->type) === 'amount' ? 'selected' : '' }}>Số tiền
                        </option>
                    </select>
                </div>
            </div>

            <!-- Trường Value -->
            <div class="mb-6">
                <label for="value" class="block text-left text-gray-700 mb-2">
                    Giá trị giảm giá <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <input type="number" step="0.01" id="value" name="value"
                        value="{{ old('value', $discount->value) }}" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập giá trị giảm giá">
                </div>
            </div>

            <!-- Thời gian bắt đầu -->
            <div class="mb-6">
                <label for="starts_at" class="block text-left text-gray-700 mb-2">Thời gian bắt đầu</label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <input type="datetime-local" id="starts_at" name="starts_at"
                        value="{{ old('starts_at', $discount->starts_at ? $discount->starts_at->format('Y-m-d\TH:i') : '') }}"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none">
                </div>
            </div>

            <!-- Thời gian hết hạn -->
            <div class="mb-6">
                <label for="expires_at" class="block text-left text-gray-700 mb-2">Thời gian hết hạn</label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <input type="datetime-local" id="expires_at" name="expires_at"
                        value="{{ old('expires_at', $discount->expires_at ? $discount->expires_at->format('Y-m-d\TH:i') : '') }}"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none">
                </div>
            </div>

            <!-- Nút submit và quay lại -->
            <div class="flex gap-4">
                <a href="{{ route('admin.discounts.show', $discount->id) }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg transition duration-200 flex-1 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại
                </a>

                <button type="button" onclick="document.getElementById('modal-edit-publisher').classList.remove('hidden')"
                    class="bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg transition duration-200 flex-1 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Cập nhật chương trình
                </button>
            </div>

            <x-confirm-modal id="modal-edit-publisher" title="Xác nhận cập nhật"
                message="Bạn có chắc chắn muốn cập nhật thông tin của chương trình này? Các sách thuộc chương trình này có thể bị ảnh hưởng"
                confirm-text="Cập nhật" cancel-text="Hủy" form-id="edit-publisher-form" action="edit" />
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
