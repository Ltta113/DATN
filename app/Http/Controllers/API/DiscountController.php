<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function getAllDiscountsWithProducts()
    {
        $discounts = Discount::with(['books' => function ($query) {
            $query->limit(5);
        }])
            ->valid()
            ->limit(3)
            ->get();

        return response()->json(
            [
                'message' => 'Danh sách chương trình khuyến mãi',
                'data' => DiscountResource::collection($discounts),
            ],
            200
        );
    }

    public function show(Discount $discount)
    {
        if (!$discount->isActive()) {
            return response()->json([
                'message' => 'Chương trình khuyến mãi không khả dụng',
            ], 404);
        }

        $books = $discount->books()->paginate(10);

        $discount->setRelation('books', $books);

        return response()->json([
            'message' => 'Chương trình khuyến mãi đã được tìm thấy',
            'data' => new DiscountResource($discount),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ]
        ], 200);
    }
}
