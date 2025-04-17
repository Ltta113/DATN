<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json(
            [
                'message' => 'Danh sách thể loại',
                'data' => CategoryResource::collection($categories),
            ],
            200
        );
    }
}
