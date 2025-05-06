<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Discount;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class DiscountController extends Controller
{

    public function create()
    {
        return view('discount.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percent,amount',
            'value' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percent' && ($value < 0 || $value >= 100)) {
                        $fail('Phần trăm giảm giá phải nhỏ hơn 100!');
                    }
                    if ($request->type === 'amount' && $value <= 100) {
                        $fail('Số tiền giảm giá phải lớn hơn 100!');
                    }
                },
            ],
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at|after:now',
        ], [
            'starts_at.date' => 'Thời gian bắt đầu không hợp lệ.',
            'expires_at.date' => 'Thời gian kết thúc không hợp lệ.',
            'expires_at.after_or_equal' => 'Thời gian kết thúc phải sau hoặc bằng thời gian bắt đầu.',
            'expires_at.after' => 'Thời gian kết thúc phải trước thời điểm hiện tại.',
            'value.required' => 'Giá trị giảm giá không được để trống.',
            'value.numeric' => 'Giá trị giảm giá phải là một số.',
            'value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'value.max' => 'Giá trị giảm giá phải nhỏ hơn 100.',
            'value.in' => 'Giá trị giảm giá không hợp lệ.',
            'name.required' => 'Tên chương trình giảm giá không được để trống.',
        ]);

        // Tạo chương trình giảm giá
        Discount::create($request->all());

        return redirect()->route('admin.discounts.index')->with('success', 'Tạo chương trình giảm giá thành công!');
    }

    public function assignToBooks(Request $request, $discountId)
    {
        // Validate input
        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
            'force' => 'sometimes|boolean',
        ]);

        // Find the discount
        $discount = Discount::findOrFail($discountId);
        $bookIds = $request->book_ids;
        $force = $request->force ?? false;

        // Get books based on IDs
        $books = Book::whereIn('id', $bookIds)->get();

        // Loop through each book and check price after discount
        foreach ($books as $book) {
            // Calculate price after discount based on discount type
            if ($discount->type === 'amount') {
                $priceAfterDiscount = $book->price - $discount->value;
            } elseif ($discount->type === 'percent') {
                $priceAfterDiscount = $book->price - ($book->price * ($discount->value / 100));
            }

            // Check if price after discount is valid (greater than 0)
            if ($priceAfterDiscount <= 0) {
                // If invalid price, return error message and redirect back with error
                return redirect()->back()->withErrors([
                    'discount_error' => "Sách '{$book->title}' có giá sau giảm không hợp lệ (<= 0)."
                ]);
            }

            if ($book->discount && !$book->discount->isExpired() && !$force) {
                return redirect()->back()->withErrors([
                    'discount_error' => "Sách '{$book->title}' đã có chương trình giảm giá khác đang hoạt động."
                ]);
            }
        }

        // Proceed with updating books if all prices are valid
        foreach ($books as $book) {
            // Skip books that already have a valid discount (unless 'force' is true)
            if ($book->discount && !$book->discount->isExpired() && !$force) {
                continue;
            }

            // Assign the discount to the book and save it
            $book->discount_id = $discount->id;
            $book->save();
        }

        // Redirect back with a success message
        return redirect()->back()->with('success_discount', 'Giảm giá đã được áp dụng thành công cho các sách.');
    }

    public function removeFromBooks(Request $request, $discountId)
    {
        // Validate input
        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
        ]);

        // Find the discount
        $discount = Discount::findOrFail($discountId);
        $bookIds = $request->book_ids;

        // Get books based on IDs
        $books = Book::whereIn('id', $bookIds)->get();

        // Loop through each book and remove the discount
        foreach ($books as $book) {
            if ($book->discount_id === $discount->id) {
                $book->discount_id = null;
                $book->save();
            }
        }

        // Redirect back with a success message
        return redirect()->back()->with('success_discount', 'Giảm giá đã được gỡ bỏ thành công cho các sách.');
    }

    /**
     * Display a listing of the discounts.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Discount::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $now = now();

            switch ($request->status) {
                case 'active':
                    $query->where('starts_at', '<=', $now)
                        ->where('expires_at', '>=', $now);
                    break;
                case 'expired':
                    $query->where('expires_at', '<', $now);
                    break;
                case 'future':
                    $query->where('starts_at', '>', $now);
                    break;
            }
        }

        $discounts = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->only(['search', 'type', 'status']));;

        return view('discount.index', compact('discounts'));
    }

    public function edit(Discount $discount)
    {
        return view('discount.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'percent' => 'sometimes|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'type' => 'sometimes|in:percent,amount',
            'value' => [
                'sometimes',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percent' && ($value < 0 || $value >= 100)) {
                        $fail('Phần trăm giảm giá phải nhỏ hơn 100!');
                    }
                    if ($request->type === 'amount' && $value <= 100) {
                        $fail('Số tiền giảm giá phải lớn hơn 100!');
                    }
                },
            ],
        ], [
            'starts_at.date' => 'Thời gian bắt đầu không hợp lệ.',
            'expires_at.date' => 'Thời gian kết thúc không hợp lệ.',
            'expires_at.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'value.required' => 'Giá trị giảm giá không được để trống.',
            'value.numeric' => 'Giá trị giảm giá phải là một số.',
            'value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'value.max' => 'Giá trị giảm giá phải nhỏ hơn 100.',
            'value.in' => 'Giá trị giảm giá không hợp lệ.',
            'name.required' => 'Tên chương trình giảm giá không được để trống.',
            'name.string' => 'Tên chương trình giảm giá phải là một chuỗi.',
            'name.max' => 'Tên chương trình giảm giá không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'type.in' => 'Loại giảm giá không hợp lệ.',
            'type.string' => 'Loại giảm giá phải là một chuỗi.',
            'type.max' => 'Loại giảm giá không được vượt quá 50 ký tự.',
        ]);

        $discount->update($validated);
        return redirect()->route('admin.discounts.index')->with('success', 'Cập nhật chương trình giảm giá thành công!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Xóa chương trình giảm giá thành công!');
    }

    public function show(Discount $discount, Request $request)
    {
        // Validate the request
        $request->validate([
            'excluded_search' => 'nullable|string|max:255',
            'tab' => 'nullable|string|in:excluded,included',
        ]);

        if ($request->has('excluded_search') && !empty($request->excluded_search)) {
            $search = $request->excluded_search;
            $bookWithoutDiscount = Book::whereDoesntHave('discount')
                ->orWhere('discount_id', '<>', $discount->id)
                ->where('title', 'like', "%{$search}%")
                ->paginate(10);
        } else {
            $bookWithoutDiscount = Book::whereDoesntHave('discount')
                ->orWhere('discount_id', '<>', $discount->id)
                ->paginate(10);
        }
        // Get all books without discount
        $bookWithDiscount = $discount->books()->paginate(10);
        // $bookWithoutDiscount = Book::whereDoesntHave('discount');

        return view('discount.show', compact('discount', 'bookWithDiscount', 'bookWithoutDiscount'));
    }
}
