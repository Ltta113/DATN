<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderFeedbackResource;
use App\Models\Order;
use App\Models\OrderFeedback;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'order_id.required' => 'Mã đơn hàng không được để trống',
            'order_id.exists' => 'Mã đơn hàng không tồn tại',
            'rating.required' => 'Đánh giá không được để trống',
            'rating.integer' => 'Đánh giá phải là số nguyên',
            'rating.min' => 'Đánh giá phải từ 1 đến 5',
            'rating.max' => 'Đánh giá phải từ 1 đến 5',
            'feedback.required' => 'Phản hồi không được để trống',
            'feedback.string' => 'Phản hồi phải là chuỗi',
            'images.*.image' => 'Ảnh phải là file ảnh',
            'images.*.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif',
            'images.*.max' => 'Ảnh phải có kích thước nhỏ hơn 2MB'
        ]);

        $order = Order::find($request->order_id);

        if ($request->user() && $order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 403);
        }

        if ($order->hasFeedback()) {
            return response()->json([
                'message' => 'Đơn hàng đã được phản hồi, không thể phản hồi lại'
            ], 403);
        }

        $cloudinary = new Cloudinary();
        $uploadApi = $cloudinary->uploadApi();

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $result = $uploadApi->upload(
                    $image->getRealPath(),
                    [
                        'folder' => 'BookStore/Orders/Feedback',
                    ]
                );

                $images[] = [
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id']
                ];
            }
        }
        $validated['images'] = json_encode($images);

        $feedback = OrderFeedback::create([
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'images' => $images
        ]);

        return response()->json([
            'message' => 'Phản hồi đã được gửi thành công',
            'data' => new OrderFeedbackResource($feedback)
        ], 201);
    }

    public function update(Request $request, OrderFeedback $orderFeedback)
    {
        $order = Order::find($orderFeedback->order_id);

        if ($request->user() && $order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'feedback' => 'sometimes|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|string'
        ], [
            'rating.integer' => 'Đánh giá phải là số nguyên',
            'rating.min' => 'Đánh giá phải từ 1 đến 5',
            'rating.max' => 'Đánh giá phải từ 1 đến 5',
            'feedback.string' => 'Phản hồi phải là chuỗi',
            'images.*.image' => 'Ảnh phải là file ảnh',
            'images.*.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif',
            'images.*.max' => 'Ảnh phải có kích thước nhỏ hơn 2MB'
        ]);

        $images = $orderFeedback->images;

        $cloudinary = new Cloudinary();
        $uploadApi = $cloudinary->uploadApi();

        if (!empty($validated['delete_images'])) {
            $deleteImages = explode(',', $validated['delete_images']);
            foreach ($deleteImages as $deleteImage) {
                $uploadApi->destroy($deleteImage);
                $images = array_filter($images, function ($image) use ($deleteImage) {
                    return $image['public_id'] !== $deleteImage;
                });
            }
        }

        if ($request->hasFile('images') && is_array($request->file('images'))) {
            foreach ($request->file('images') as $image) {
                $result = $uploadApi->upload(
                    $image->getRealPath(),
                    [
                        'folder' => 'BookStore/Orders/Feedback',
                    ]
                );

                $images[] = [
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id']
                ];
            }
        }

        $orderFeedback->update([
            'rating' => $validated['rating'] ?? $orderFeedback->rating,
            'feedback' => $validated['feedback'] ?? $orderFeedback->feedback,
            'images' => $images
        ]);

        return response()->json([
            'message' => 'Phản hồi đã được cập nhật thành công',
            'data' => new OrderFeedbackResource($orderFeedback)
        ]);
    }

    public function destroy(OrderFeedback $orderFeedback)
    {
        $cloudinary = new Cloudinary();
        $uploadApi = $cloudinary->uploadApi();

        if (!empty($orderFeedback->images)) {
            foreach ($orderFeedback->images as $image) {
                $uploadApi->destroy($image['public_id']);
            }
        }

        $orderFeedback->delete();

        return response()->json([
            'message' => 'Phản hồi đã được xóa thành công'
        ]);
    }

    public function show(OrderFeedback $orderFeedback)
    {
        return response()->json([
            'data' => $orderFeedback
        ]);
    }
}
