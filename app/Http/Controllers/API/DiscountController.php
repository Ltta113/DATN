<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function getListDiscounts()
    {
        $discounts = Discount::valid()
            ->hasBooks()
            ->paginate(10);
        return response()->json(
            [
                'message' => 'Danh sách chương trình khuyến mãi',
                'data' => DiscountResource::collection($discounts),
                'pagination' => [
                    'current_page' => $discounts->currentPage(),
                    'last_page' => $discounts->lastPage(),
                    'per_page' => $discounts->perPage(),
                    'total' => $discounts->total(),
                ]
            ],
            200
        );
    }

    public function getAllDiscountsWithProducts()
    {
        $discounts = Discount::with(['books' => function ($query) {
            $query->limit(5);
        }])
            ->valid()
            ->hasBooks()
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

        if (!$discount->hasBooks()) {
            return response()->json([
                'message' => 'Chương trình khuyến mãi không có sách',
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
