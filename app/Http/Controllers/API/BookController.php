<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
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

        return response()->json(
            [
                'message' => 'Danh sách sách mới nhất',
                'data' => BookResource::collection($books),
            ],
            200
        );
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
     * Search books by name or author name with pagination.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $booksQuery = Book::with(['publisher', 'book_authors', 'book_categories']);

        if ($request->filled('name')) {
            $booksQuery->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->name . '%')
                    ->orWhereHas('book_authors', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->name . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $booksQuery->whereHas('book_categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $books = $booksQuery->paginate(10);

        $authors = $request->filled('name')
            ? Author::where('name', 'like', '%' . $request->name . '%')
            ->limit(3)
            ->get()
            : collect();

        return response()->json([
            'message' => 'Danh sách sách và tác giả tìm kiếm',
            'data' => [
                'books' => BookResource::collection($books),
                'authors' => AuthorResource::collection($authors),
            ],
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }
}
