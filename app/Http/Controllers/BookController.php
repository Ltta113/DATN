<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
