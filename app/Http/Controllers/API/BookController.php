<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
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
}
