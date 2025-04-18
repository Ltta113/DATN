<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $query = Publisher::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $query->withCount('books');

        $publishers = $query
            ->orderBy('books_count', 'desc')
            ->paginate(10);

        return view('publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('publishers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:publishers,name',
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'website' => 'nullable|url',
            ],
            [
                'name.required' => 'Tên nhà xuất bản không được để trống.',
                'name.string' => 'Tên nhà xuất bản phải là một chuỗi.',
                'name.max' => 'Tên nhà xuất bản không được vượt quá 255 ký tự.',
                'name.unique' => 'Tên nhà xuất bản đã tồn tại.',
            ]
        );

        if ($request->hasFile('logo')) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('logo')->getRealPath(),
                [
                    'folder' => 'BookStore/Publishers',
                ]
            );

            $validated['logo'] = $result['secure_url'];
        }
        $validated['public_id'] = $result['public_id'];
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        Publisher::create($validated);

        return redirect()->route('admin.publishers.index')->with('success', 'Thêm nhà xuất bản thành công.');
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Publisher::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function show(Publisher $publisher)
    {
        $publisher->load('books');

        $books = $publisher->books()
            ->with(['book_authors', 'publisher'])
            ->paginate(12);

        return view('publishers.show', compact('publisher', 'books'));
    }

    public function edit($id)
    {
        $publisher = Publisher::findOrFail($id);

        return view('publishers.edit', compact('publisher'));
    }

    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:publishers,name,' . $publisher->id,
                'description' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'website' => 'nullable|url',
            ],
            [
                'name.required' => 'Tên nhà xuất bản không được để trống.',
                'name.string' => 'Tên nhà xuất bản phải là một chuỗi.',
                'name.max' => 'Tên nhà xuất bản không được vượt quá 255 ký tự.',
                'name.unique' => 'Tên nhà xuất bản đã tồn tại.',
            ]
        );

        if ($request->hasFile('logo')) {
            $cloudinary = new Cloudinary();
            if ($publisher->public_id) {
                $cloudinary->uploadApi()->destroy($publisher->public_id);
            }
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('logo')->getRealPath(),
                [
                    'folder' => 'BookStore/Publishers',
                ]
            );

            $validated['logo'] = $result['secure_url'];
        }

        $publisher->update($validated);

        return redirect()->route('admin.publishers.index')->with('success', 'Cập nhật nhà xuất bản thành công.');
    }

    public function destroy(Publisher $publisher)
    {
        if ($publisher->public_id) {
            $cloudinary = new Cloudinary();
            $cloudinary->uploadApi()->destroy($publisher->public_id);
        }

        $publisher->delete();

        return redirect()->route('admin.publishers.index')->with('success', 'Xóa nhà xuất bản thành công.');
    }
}
