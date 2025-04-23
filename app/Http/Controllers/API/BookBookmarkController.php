<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class BookBookmarkController extends Controller
{
    public function toggleBookmark(Request $request, Book $book)
    {
        $userId = $request->user()->id;
        $bookId = $book->id;

        if (!$userId) {
            return response()->json([
                'message' => 'Cần đăng nhập để thực hiện thao tác này',
            ], 401);
        }

        $user = User::find($userId);

        if ($user->bookmarks()->where('book_id', $bookId)->exists()) {
            $user->bookmarks()->detach($bookId);
            return response()->json(
                [
                    'message' => 'Bỏ bookmark thành công',
                    'data' => [
                        'books' => BookResource::collection($user->bookmarks),
                    ],
                ],
                200
            );
        } else {
            $user->bookmarks()->attach($bookId);
            return response()->json(
                [
                    'message' => 'Bookmark thành công',
                    'data' => [
                        'books' => BookResource::collection($user->bookmarks),
                    ],
                ],
                200
            );
        }
    }
}
