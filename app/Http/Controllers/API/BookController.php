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
    public function getNewestBooks(Request $request): JsonResponse
    {
        $minPrice = $request->query('min', 0);
        $maxPrice = $request->query('max', 2500000);

        $booksQuery = Book::with(['publisher', 'authors', 'categories'])
            ->getNewestBooks();

        if ($minPrice && $maxPrice) {
            $booksQuery->whereBetween('price', [$minPrice, $maxPrice]);
        }

        $books = $booksQuery->paginate(10);

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Không có sách nào',
            ], 200);
        }

        return response()->json([
            'message' => 'Danh sách sách mới nhất',
            'data' => BookResource::collection($books),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
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
        $book = Book::with(['publisher', 'authors', 'categories', 'reviews.user'])
            ->where('slug', $slug)
            ->where('status', 'active')
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
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
        ]);

        $booksQuery = Book::with(['publisher', 'authors', 'categories'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        if ($request->filled('name')) {
            $booksQuery->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->name . '%')
                    ->orWhereHas('authors', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->name . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $booksQuery->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('min') && $request->filled('max')) {
            $booksQuery->whereBetween('price', [$request->min, $request->max]);
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

    public function getListBestSoldBooks(Request $request): JsonResponse
    {
        $books = Book::with(['publisher', 'authors', 'categories'])
            ->getBestSoldBooks()
            ->paginate(10);

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Không có sách nào',
            ], 404);
        }

        return response()->json([
            'message' => 'Danh sách sách bán chạy nhất',
            'data' => BookResource::collection($books),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }
}
