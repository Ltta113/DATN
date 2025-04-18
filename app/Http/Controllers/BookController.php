<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Book::with(['publisher', 'book_authors', 'book_categories']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('category') && $request->category) {
            $query->whereHas('book_categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        if ($request->has('publisher') && $request->publisher) {
            $query->where('publisher_id', $request->publisher);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        $books = $query->paginate(10);

        $categories = Category::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();

        return view('books.index', compact('books', 'categories', 'publishers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        $publishers = Publisher::all();

        return view('books.create', compact('authors', 'categories', 'publishers'));
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'title'         => 'required|string|max:255',
                'description'   => 'nullable|string',
                'published_at'  => 'nullable|date',
                'publisher_id'  => 'nullable|exists:publishers,id',
                'cover_image'   => 'required|image|max:2048|mimes:jpeg,png,jpg',
                'price'         => 'nullable|numeric',
                'stock'         => 'nullable|integer',
                'page_count'    => 'nullable|integer',
                'author_ids'    => 'nullable|array',
                'author_ids.*'  => 'exists:authors,id',
                'category_ids'  => 'nullable|array',
                'category_ids.*' => 'exists:categories,id',
            ],
            [
                'title.required' => 'Tên sách không được để trống.',
                'title.string' => 'Tên sách phải là một chuỗi.',
                'title.max' => 'Tên sách không được vượt quá 255 ký tự.',
                'description.string' => 'Mô tả phải là một chuỗi.',
                'published_at.date' => 'Ngày xuất bản không hợp lệ.',
                'publisher_id.exists' => 'Nhà xuất bản không tồn tại.',
                'cover_image.required' => 'Hình ảnh bìa sách không được để trống.',
                'cover_image.image' => 'Hình ảnh bìa sách không hợp lệ.',
                'cover_image.max' => 'Kích thước hình ảnh bìa sách không được vượt quá 2MB.',
                'cover_image.mimes' => 'Hình ảnh bìa sách phải có định dạng jpeg, png hoặc jpg.',
                'price.numeric' => 'Giá sách phải là một số.',
                'stock.integer' => 'Số lượng sách phải là một số nguyên.',
                'page_count.integer' => 'Số trang sách phải là một số nguyên.',
                'author_ids.array' => 'Tác giả không hợp lệ.',
                'author_ids.*.exists' => 'Tác giả không tồn tại.',
                'category_ids.array' => 'Thể loại không hợp lệ.',
                'category_ids.*.exists' => 'Thể loại không tồn tại.',
            ]
        );

        $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        $validated['status'] = 'inactive';

        if ($request->hasFile('cover_image')) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('cover_image')->getRealPath(),
                [
                    'folder' => 'BookStore/Books',
                ]
            );

            $validated['cover_image'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }

        $book = Book::create($validated);

        if (!empty($validated['author_ids'])) {
            $book->book_authors()->sync($validated['author_ids']);
        }

        if (!empty($validated['category_ids'])) {
            $book->book_categories()->sync($validated['category_ids']);
        }

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Tạo mới sách thành công');
    }

    /**
     * Generate a unique slug from a title.
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Book::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Display the specified resource.
     *
     * @param Book $book
     *
     * @return View
     */
    public function showByAdmin(Book $book): View
    {
        $book->load(['publisher', 'book_authors', 'book_categories']);

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        $book->load(['publisher', 'book_authors', 'book_categories']);

        $authors = Author::all();
        $categories = Category::all();
        $publishers = Publisher::all();

        return view('books.edit', compact('book', 'authors', 'categories', 'publishers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'published_at'  => 'nullable|date',
            'publisher_id'  => 'exists:publishers,id',
            'cover_image'   => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
            'price'         => 'nullable|numeric',
            'stock'         => 'nullable|integer',
            'page_count'    => 'nullable|integer',
            'author_ids'    => 'array',
            'author_ids.*'  => 'exists:authors,id',
            'category_ids'  => 'array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $cloudinary = new Cloudinary();
            if ($book->public_id) {
                $cloudinary->uploadApi()->destroy($book->public_id);
            }
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('cover_image')->getRealPath(),
                [
                    'folder' => 'BookStore/Books',
                ]
            );

            $validated['cover_image'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }

        $book->update($validated);

        if (!empty($validated['author_ids'])) {
            $book->book_authors()->sync($validated['author_ids']);
        }

        if (!empty($validated['category_ids'])) {
            $book->book_categories()->sync($validated['category_ids']);
        }

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Cập nhật sách thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeStatus(Request $request, Book $book)
    {
        $validated = $request->validate([
            'status' => 'required|in:inactive,active,sold_out,deleted',
        ]);

        $book->update($validated);

        return redirect()->route('admin.books.show', $book)
            ->with('success', 'Cập nhật trạng thái sách thành công');
    }
}
