<div class="container">
    <h1>Thêm sách mới</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label>Tiêu đề:</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>

        <div>
            <label>Mô tả:</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <div>
            <label>ISBN:</label>
            <input type="text" name="isbn" value="{{ old('isbn') }}" required>
        </div>

        <div>
            <label>Ngày xuất bản:</label>
            <input type="date" name="published_at" value="{{ old('published_at') }}">
        </div>

        <div>
            <label>Nhà xuất bản:</label>
            <select name="publisher_id">
                <option value="">-- Chọn --</option>
                @foreach ($publishers as $publisher)
                    <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Hình ảnh bìa:</label>
            <input type="file" name="cover_image">
        </div>

        <div>
            <label>Giá:</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}">
        </div>

        <div>
            <label>Số lượng tồn:</label>
            <input type="number" name="stock" value="{{ old('stock') }}">
        </div>

        <div>
            <label>Ngôn ngữ:</label>
            <input type="text" name="language" value="{{ old('language') }}">
        </div>

        <div>
            <label>Số trang:</label>
            <input type="number" name="page_count" value="{{ old('page_count') }}">
        </div>

        <div>
            <label>Tác giả:</label>
            <select name="author_ids[]" multiple>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Thể loại:</label>
            <select name="category_ids[]" multiple>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Lưu sách</button>
    </form>
</div>
