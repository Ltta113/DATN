@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Chỉnh sửa Nhà Xuất Bản</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="edit-publisher-form" action="{{ route('admin.publishers.update', $publisher->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Tên nhà xuất bản -->
            <div class="mb-6">
                <label for="name" class="block text-left text-gray-700 mb-2">
                    Tên nhà xuất bản <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <input type="text" id="name" name="name" value="{{ old('name', $publisher->name) }}" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tên nhà xuất bản">
                </div>
            </div>

            <!-- Website -->
            <div class="mb-6">
                <label for="website" class="block text-left text-gray-700 mb-2">Website</label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10
                                   10-4.477 10-10S17.523 2 12 2zm0 0c2.21 0 4 4.03 4 9s-1.79 9-4 9
                                   -4-4.03-4-9 1.79-9 4-9zm0 0v18m-9-9h18" />
                    </svg>
                    <input type="url" id="website" name="website" value="{{ old('website', $publisher->website) }}"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="https://example.com"
                        pattern="https?://.*" title="Vui lòng nhập URL hợp lệ, bắt đầu bằng http:// hoặc https://">
                </div>
                <div class="mt-4 {{ $publisher->website ? '' : 'hidden' }}" id="website_preview_container">
                    <p class="text-sm text-gray-500 mb-2">Xem trước trang web:</p>
                    <iframe id="website_preview" src="{{ $publisher->website }}"
                        class="w-full border rounded-lg h-[300px]"></iframe>
                </div>
            </div>

            <!-- Logo -->
            <div>
                <label for="logo" class="block text-left text-gray-700 mb-2">Logo</label>
                <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg my-8">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-full h-64 mb-4 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center">
                            <img id="imagePreview" src="{{ $publisher->logo ?? '/api/placeholder/200/300' }}" alt="Logo"
                                class="h-full object-contain">
                        </div>
                        <div class="bg-gray-100 rounded-lg flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input type="file" id="logo" name="logo" accept="image/*"
                                class="bg-transparent w-full py-3 px-4 text-left outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100"
                                onchange="previewImage(event)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mb-6">
                <label for="description" class="block text-left text-gray-700 mb-2">Mô tả</label>
                <div class="bg-gray-100 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-gray-500 mx-3 mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <textarea id="description" name="description" rows="4"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập mô tả nhà xuất bản">{{ old('description', $publisher->description) }}</textarea>
                </div>
            </div>

            <!-- Nút xác nhận -->
            <button type="button" onclick="document.getElementById('modal-edit-publisher').classList.remove('hidden')"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg mt-4 transition duration-200 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Cập nhật Nhà Xuất Bản
            </button>

            <!-- Modal xác nhận -->
            <x-confirm-modal id="modal-edit-publisher" title="Xác nhận cập nhật"
                message="Bạn có chắc chắn muốn cập nhật thông tin nhà xuất bản này?" confirm-text="Cập nhật"
                cancel-text="Hủy" form-id="edit-publisher-form" action="edit" />
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = e => imagePreview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('website');
            const previewContainer = document.getElementById('website_preview_container');
            const previewFrame = document.getElementById('website_preview');

            function updatePreview() {
                const url = input.value.trim();
                if (url.startsWith('http://') || url.startsWith('https://')) {
                    previewFrame.src = url;
                    previewContainer.classList.remove('hidden');
                } else {
                    previewFrame.src = '';
                    previewContainer.classList.add('hidden');
                }
            }

            input.addEventListener('input', updatePreview);
        });
    </script>
@endsection
