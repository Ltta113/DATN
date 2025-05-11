<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Combo;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewable_type' => 'required|string|in:author,book,order,combo',
            'reviewable_id'   => 'required|integer',
            'content'         => 'required|string|max:1000',
            'rating'          => 'required|integer|min:1|max:5',
        ], [
            'reviewable_id.required' => 'Trường ID là bắt buộc.',
            'content.required' => 'Trường nội dung là bắt buộc.',
            'rating.required' => 'Trường đánh giá là bắt buộc.',
            'rating.integer' => 'Đánh giá phải là một số nguyên.',
            'rating.min' => 'Đánh giá phải ít nhất là 1.',
            'rating.max' => 'Đánh giá không được lớn hơn 5.',
            'content.max' => 'Nội dung không được vượt quá 1000 ký tự.',
            'reviewable_type.in' => 'Loại đối tượng đánh giá được chọn không hợp lệ.',
        ]);

        $modelMap = [
            'author' => Author::class,
            'book'   => Book::class,
            'order'  => Order::class,
            'combo'  => Combo::class,
        ];

        $modelClass = $modelMap[$validated['reviewable_type']] ?? null;

        if (!$modelClass) {
            return response()->json(
                ['message' => '
                Loại đối tượng đánh giá không hợp lệ.
            '],
                400
            );
        }

        $reviewable = $modelClass::find($validated['reviewable_id']);

        if (!$reviewable) {
            return response()->json(
                [
                    'message' => 'Đối tượng đánh giá không tồn tại.'
                ],
                404
            );
        }

        $existingReview = $reviewable->reviews()->where('user_id', $request->user()->id)->first();
        if ($existingReview) {
            return response()->json(
                ['message' => 'Bạn đã đánh giá đối tượng này rồi.'],
                400
            );
        }


        $review = $reviewable->reviews()->create([
            'content' => $validated['content'],
            'rating'  => $validated['rating'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Đánh giá đã được tạo thành công.',
            'data'    => $review
        ], 201);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'reviewable_type' => 'required|string|in:author,book,order,combo',
            'reviewable_id'   => 'required|integer',
            'content'         => 'required|string|max:1000',
            'rating'          => 'required|integer|min:1|max:5',
        ], [
            'reviewable_id.required' => 'Trường ID là bắt buộc.',
            'content.required' => 'Trường nội dung là bắt buộc.',
            'rating.required' => 'Trường đánh giá là bắt buộc.',
            'rating.integer' => 'Đánh giá phải là một số nguyên.',
            'rating.min' => 'Đánh giá phải ít nhất là 1.',
            'rating.max' => 'Đánh giá không được lớn hơn 5.',
            'content.max' => 'Nội dung không được vượt quá 1000 ký tự.',
            'reviewable_type.in' => 'Loại đối tượng đánh giá không hợp lệ.',
        ]);

        $modelMap = [
            'author' => Author::class,
            'book'   => Book::class,
            'order'  => Order::class,
            'combo'  => Combo::class,
        ];

        $modelClass = $modelMap[$validated['reviewable_type']] ?? null;

        if (!$modelClass) {
            return response()->json([
                'message' => 'Loại đối tượng đánh giá không hợp lệ.'
            ], 400);
        }

        $reviewable = $modelClass::find($validated['reviewable_id']);

        if (!$reviewable) {
            return response()->json([
                'message' => 'Đối tượng đánh giá không tồn tại.'
            ], 404);
        }

        $existingReview = $reviewable->reviews()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$existingReview) {
            return response()->json([
                'message' => 'Bạn chưa có đánh giá để cập nhật.'
            ], 404);
        }

        $existingReview->update([
            'content' => $validated['content'],
            'rating'  => $validated['rating'],
        ]);

        return response()->json([
            'message' => 'Đánh giá đã được cập nhật thành công.',
            'data'    => $existingReview
        ], 200);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'reviewable_type' => 'required|string|in:author,book,order,combo',
            'reviewable_id'   => 'required|integer',
        ], [
            'reviewable_id.required' => 'Trường ID là bắt buộc.',
            'reviewable_type.in' => 'Loại đối tượng đánh giá không hợp lệ.',
        ]);

        $modelMap = [
            'author' => Author::class,
            'book'   => Book::class,
            'order'  => Order::class,
            'combo'  => Combo::class,
        ];

        $modelClass = $modelMap[$validated['reviewable_type']] ?? null;

        if (!$modelClass) {
            return response()->json([
                'message' => 'Loại đối tượng đánh giá không hợp lệ.'
            ], 400);
        }

        $reviewable = $modelClass::find($validated['reviewable_id']);

        if (!$reviewable) {
            return response()->json([
                'message' => 'Đối tượng đánh giá không tồn tại.'
            ], 404);
        }

        $existingReview = $reviewable->reviews()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$existingReview) {
            return response()->json([
                'message' => 'Bạn chưa có đánh giá để xóa.'
            ], 404);
        }

        $existingReview->delete();

        return response()->json([
            'message' => 'Đánh giá đã được xóa thành công.'
        ], 200);
    }
}
