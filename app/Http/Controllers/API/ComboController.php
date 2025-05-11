<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComboResource;
use App\Models\Combo;
use App\Models\Review;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function getBestSoldCombos()
    {
        $combos = Combo::orderBy('sold', 'desc')->take(10)->get();
        return response()->json([
            'message' => 'Lấy danh sách combo bán chạy thành công',
            'data' => ComboResource::collection($combos),
        ]);
    }

    public function getBestSoldCombosThisMonth()
    {
        $combos = Combo::orderBy('sold', 'desc')->whereMonth('created_at', now()->month)->take(10)->get();
        return response()->json([
            'message' => 'Lấy danh sách combo bán chạy tháng này thành công',
            'data' => ComboResource::collection($combos),
        ]);
    }

    public function getListCombos(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $combos = Combo::orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json([
            'message' => 'Lấy danh sách combo thành công',
            'data' => ComboResource::collection($combos),
            'pagination' => [
                'current_page' => $combos->currentPage(),
                'last_page' => $combos->lastPage(),
                'per_page' => $combos->perPage(),
                'total' => $combos->total(),
            ],
        ]);
    }

    public function getComboDetail($slug)
    {
        $combo = Combo::with(['books', 'reviews.user'])->where('slug', $slug)->first();
        if (!$combo) {
            return response()->json([
                'message' => 'Combo không tồn tại',
            ], 404);
        }
        return response()->json([
            'message' => 'Lấy chi tiết combo thành công',
            'data' => new ComboResource($combo),
        ]);
    }
}
