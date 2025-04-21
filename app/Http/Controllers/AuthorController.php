<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::query();

        if ($request->has('search')) {
            $searchTerm = request()->input('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $query->withCount('books');
        $authors = $query->orderBy('books_count', 'desc')
            ->paginate(10)
            ->appends($request->only(['search']));

        return view('authors.index', compact('authors'));
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'biography' => 'nullable|string',
                'birth_date' => 'nullable|date',
            ],
            [
                'name.required' => 'Tên tác giả không được để trống.',
                'name.string' => 'Tên tác giả phải là một chuỗi.',
                'name.max' => 'Tên tác giả không được vượt quá 255 ký tự.',
                'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
                'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png hoặc jpg.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
                'biography.string' => 'Tiểu sử phải là một chuỗi.',
                'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            ]
        );

        if ($request->hasFile('photo')) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('photo')->getRealPath(),
                [
                    'folder' => 'BookStore/Authors',
                ]
            );

            $validated['photo'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        Author::create($validated);

        return redirect()->route('admin.authors.index')->with('success', 'Tác giả đã được thêm thành công.');
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Author::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function show(Author $author)
    {
        $author->load('books');

        $books = $author->books()
            ->with(['authors', 'publisher'])
            ->paginate(12);

        return view('authors.show', compact('author', 'books'));
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'biography' => 'nullable|string',
                'birth_date' => 'nullable|date',
            ],
            [
                'name.required' => 'Tên tác giả không được để trống.',
                'name.string' => 'Tên tác giả phải là một chuỗi.',
                'name.max' => 'Tên tác giả không được vượt quá 255 ký tự.',
                'avatar.image' => 'Ảnh đại diện phải là một hình ảnh.',
                'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png hoặc jpg.',
                'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
                'biography.string' => 'Tiểu sử phải là một chuỗi.',
                'birth_date.date' => 'Ngày sinh phải là một ngày hợp lệ.',
            ]
        );

        if ($request->hasFile('photo')) {
            $cloudinary = new Cloudinary();
            if ($author->public_id) {
                $cloudinary->uploadApi()->destroy($author->public_id);
            }
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('photo')->getRealPath(),
                [
                    'folder' => 'BookStore/Authors',
                ]
            );

            $validated['photo'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }

        $author->update($validated);

        return redirect()->route('admin.authors.index')->with('success', 'Tác giả đã được cập nhật thành công.');
    }

    public function destroy(Author $author)
    {
        if ($author->public_id) {
            $cloudinary = new Cloudinary();
            $cloudinary->uploadApi()->destroy($author->public_id);
        }

        $author->delete();

        return redirect()->route('admin.authors.index')
            ->with('success', 'Tác giả đã được xóa thành công.');
    }
}
