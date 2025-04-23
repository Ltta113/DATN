<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\BookResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthorController extends Controller
{
    public function getAuthorManyBooks()
    {
        $authors = Author::withCount(['books' => function ($query) {
            $query->where('status', 'active');
        }])
            ->orderBy('books_count', 'desc')
            ->take(6)
            ->get();

        return response()->json([
            'message' => "Top tác giả nổi bật nhất",
            'data' => AuthorResource::collection($authors),
        ]);
    }

    public function index()
    {

        $authors = Author::withCount(['books' => function ($query) {
            $query->where('books.status', 'active');
        }])
            ->orderBy('books_count', 'desc')
            ->paginate(12);

        return response()->json([
            'message' => "Danh sách tác giả",
            'data' => AuthorResource::collection($authors),
            'pagination' => [
                'total' => $authors->total(),
                'current_page' => $authors->currentPage(),
                'last_page' => $authors->lastPage(),
                'per_page' => $authors->perPage(),
            ],
        ]);
    }

    public function show($slug)
    {
        $author = Author::withCount(['books' => function ($query) {
            $query->where('books.status', 'active');
        }])
            ->orderBy('books_count', 'desc')
            ->where('slug', $slug)
            ->first();

        if (!$author) {
            return response()->json([
                'message' => 'Tác giả không tồn tại.',
                'data' => null
            ], 404);
        }

        $books = $author->books()
            ->with(['authors', 'publisher', 'categories'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->where('status', '<>', 'deleted')
            ->paginate(12);

        return response()->json([
            'message' => "Thông tin tác giả",
            'data' => [
                'author' => new AuthorResource($author),
                'books' =>  BookResource::collection($books),
            ],
            'pagination' => [
                'total' => $books->total(),
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
            ],
        ]);
    }
}
