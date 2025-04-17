<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
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
    public function index()
    {
        //
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
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'isbn'          => 'required|string|unique:books,isbn',
            'published_at'  => 'nullable|date',
            'publisher_id'  => 'nullable|exists:publishers,id',
            'cover_image'   => 'nullable|image|max:2048',
            'price'         => 'nullable|numeric',
            'stock'         => 'nullable|integer',
            'language'      => 'nullable|string|max:100',
            'page_count'    => 'nullable|integer',
            'author_ids'    => 'nullable|array',
            'author_ids.*'  => 'exists:authors,id',
            'category_ids'  => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        // Handle cover image upload if exists
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('book_covers', 'public');
            $validated['cover_image'] = $imagePath;
        }

        // Tạo slug duy nhất
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        // Tạo sách
        $book = Book::create($validated);

        // Gắn authors
        if (!empty($validated['author_ids'])) {
            $book->book_authors()->sync($validated['author_ids']);
        }

        // Gắn categories
        if (!empty($validated['category_ids'])) {
            $book->book_categories()->sync($validated['category_ids']);
        }

        return response()->json([
            'message' => 'Book created successfully.',
            'book' => $book->load('book_authors', 'book_categories'),
        ], 201);
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
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $book = Book::with(['publisher', 'book_authors', 'book_categories'])
            ->where('slug', $slug)
            ->first();

        if (!$book) {
            return response()->json(
                [
                    'message' => 'Sách không tồn tại',
                ],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Thông tin sách',
                'data' => new BookResource($book),
            ],
            200
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    /**
     * Get the newest books.
     *
     * @param int $limit
     *
     * @return JsonResponse
     */
    public function getNewestBooks($limit = 10): JsonResponse
    {
        $books = Book::with(['publisher', 'book_authors', 'book_categories'])->getNewestBooks($limit);

        // return response()->json($books, 200);
        return response()->json(
            [
                'message' => 'Danh sách sách mới nhất',
                'data' => BookResource::collection($books),
            ],
            200
        );
    }
}
