<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Category::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%");
        }

        $query->withCount('books');

        $categories = $query
            ->orderBy('books_count', 'desc')
            ->paginate(10)
            ->appends($request->only(['search']));

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
            ],
            [
                'name.required' => 'Tên danh mục không được để trống.',
                'name.string' => 'Tên danh mục phải là một chuỗi.',
                'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'name.unique' => 'Tên danh mục đã tồn tại.',
                'description.string' => 'Mô tả phải là một chuỗi.',
                'parent_id.exists' => 'Danh mục cha không tồn tại.',
            ]
        );

        Category::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'slug' => $this->generateUniqueSlug($request->input('name')),
            'parent_id' => $request->input('parent_id'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', '
            Thêm danh mục thành công.');
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return View
     */
    public function show(Category $category): View
    {
        $category->load('books');

        $books = $category->books()
            ->with(['authors', 'publisher'])
            ->paginate(12);

        return view('categories.show', compact('category', 'books'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
            ],
            [
                'name.required' => 'Tên danh mục không được để trống.',
                'name.string' => 'Tên danh mục phải là một chuỗi.',
                'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
                'name.unique' => 'Tên danh mục đã tồn tại.',
                'description.string' => 'Mô tả phải là một chuỗi.',
                'parent_id.exists' => 'Danh mục cha không tồn tại.',
            ]
        );

        $category->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'slug' => $this->generateUniqueSlug($request->input('name')),
            'parent_id' => $request->input('parent_id'),
        ]);

        return redirect()->route(
            'admin.categories.show',
            [
                'category' => $category,
            ]
        )->with('success', '
            Cập nhật danh mục thành công.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
