@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold text-center mb-8">Chỉnh sửa Danh Mục</h1>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-lg">
                <ul class="list-disc pl-5 text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="edit-category-form" action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tên danh mục -->
            <div class="mb-6">
                <label for="name" class="block text-left text-gray-700 mb-2">
                    Tên danh mục <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-100 rounded-lg flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                        class="bg-transparent w-full py-3 px-4 text-left outline-none" placeholder="Nhập tên danh mục">
                </div>
            </div>

            <!-- Mô tả -->
            <div class="mb-6">
                <label for="description" class="block text-left text-gray-700 mb-2">Mô tả</label>
                <div class="bg-gray-100 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-gray-500 mx-3 mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <textarea id="description" name="description" rows="4"
                        class="bg-transparent w-full py-3 px-4 text-left outline-none resize-none" placeholder="Nhập mô tả danh mục">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('modal-edit-confirm').classList.remove('hidden')"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg mt-4 hover:bg-blue-700">
                💾 Lưu thay đổi
            </button>

            <x-confirm-modal id="modal-edit-confirm" title="Xác nhận cập nhật"
                message="Bạn có chắc muốn cập nhật thông tin danh mục này?
                    Việc này sẽ làm thay đổi thông tin sách thuộc danh mục này.
                "
                confirm-text="Lưu" cancel-text="Hủy" form-id="edit-category-form" action="edit"/>
        </form>
    </div>
@endsection
