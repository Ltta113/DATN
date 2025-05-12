<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Combo;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComboController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::with('books');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
        // Lọc theo trạng thái
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        $combos = $query->paginate(10)->withQueryString();
        return view('combos.index', compact('combos'));
    }

    public function show(string $id)
    {
        $combo = Combo::withTrashed()->findOrFail($id);
        $books = $combo->books;
        return view('combos.show', compact('combo', 'books'));
    }

    public function create()
    {
        $query = Book::where('status', 'active');

        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        $query->orderBy('title');
        $books = $query->paginate(20);

        // Lấy danh sách sách đã chọn từ session
        $selectedBooks = session('selected_books', []);

        if (request()->ajax()) {
            return view('combos._books_table', compact('books', 'selectedBooks'))->render();
        }

        return view('combos.create', compact('books', 'selectedBooks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'books' => 'required|array',
            'books.*' => 'exists:books,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'image.required' => 'Hình ảnh combo không được để trống.',
            'image.image' => 'Hình ảnh combo phải là một file ảnh.',
            'image.mimes' => 'Hình ảnh combo phải có định dạng jpeg, png hoặc jpg.',
            'image.max' => 'Kích thước hình ảnh combo không được vượt quá 2MB.',
            'books.required' => 'Combo phải có ít nhất 2 sách.',
            'books.array' => 'Combo phải là một mảng sách.',
            'books.*.exists' => 'Một số sách không tồn tại.',
            'name.required' => 'Tên combo không được để trống.',
            'name.string' => 'Tên combo phải là một chuỗi.',
            'name.max' => 'Tên combo không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả combo phải là một chuỗi.',
            'price.required' => 'Giá combo không được để trống.',
            'price.numeric' => 'Giá combo phải là một số.',
            'price.min' => 'Giá combo phải lớn hơn 0.',
            'books.min' => 'Combo phải có ít nhất 2 sách.',
        ]);

        $error = null;
        if (count($validated['books']) < 2) {
            $error = 'Combo phải có ít nhất 2 sách.';
        } else {
            $books = Book::whereIn('id', $validated['books'])->get();
            if ($books->count() !== count($validated['books'])) {
                $error = 'Một số sách không tồn tại.';
            } else {
                foreach ($books as $book) {
                    if ($book->stock < 1) {
                        $error .= '\nSách "' . $book->title . '" không còn trong kho. ';
                    }

                    if ($book->published_at > now()) {
                        $error .= '\nSách "' . $book->title . '" chưa được phát hành. ';
                    }
                }
            }
        }

        if ($error) {
            return redirect()->route('admin.combos.create')->with('error_combo', $error);
        }

        if ($request->hasFile('image')) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            $result = $uploadApi->upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'BookStore/Combos'
                ]
            );
            $validated['image'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }

        $validated['slug'] = Combo::generateSlug($validated['name']);

        $combo = Combo::create($validated);
        $combo->books()->attach($validated['books']);

        // Xóa session sau khi tạo combo thành công
        session()->forget('selected_books');

        return redirect()->route('admin.combos.index')->with('success_combo', 'Combo đã được tạo thành công.');
    }

    public function edit(Combo $combo)
    {
        $books = Book::where('status', 'active')
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->paginate(10);

        // Lấy danh sách sách đã chọn từ session hoặc từ combo hiện tại
        $selectedBooks = session('selected_books', $combo->books->pluck('price', 'id')->toArray());

        if (request()->ajax()) {
            return view('combos._books_table', compact('books', 'selectedBooks'));
        }

        return view('combos.edit', compact('combo', 'books', 'selectedBooks'));
    }

    public function update(Request $request, Combo $combo)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'books' => 'required|array',
            'books.*' => 'exists:books,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'image.image' => 'Hình ảnh combo phải là một file ảnh.',
            'image.mimes' => 'Hình ảnh combo phải có định dạng jpeg, png hoặc jpg.',
            'image.max' => 'Kích thước hình ảnh combo không được vượt quá 2MB.',
            'books.min' => 'Combo phải có ít nhất 2 sách.',
            'books.array' => 'Combo phải là một mảng sách.',
            'books.*.exists' => 'Một số sách không tồn tại.',
            'name.required' => 'Tên combo không được để trống.',
            'name.string' => 'Tên combo phải là một chuỗi.',
            'name.max' => 'Tên combo không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả combo phải là một chuỗi.',
            'price.required' => 'Giá combo không được để trống.',
            'price.numeric' => 'Giá combo phải là một số.',
            'price.min' => 'Giá combo phải lớn hơn 0.',
        ]);

        $error = null;
        if (count($validated['books']) < 2) {
            $error = 'Combo phải có ít nhất 2 sách.';
        } else {
            $books = Book::whereIn('id', $validated['books'])->get();
            if ($books->count() !== count($validated['books'])) {
                $error = 'Một số sách không tồn tại.';
            } else {
                foreach ($books as $book) {
                    if ($book->stock < 1) {
                        $error .= '\nSách "' . $book->title . '" không còn trong kho. ';
                    }

                    if ($book->published_at > now()) {
                        $error .= '\nSách "' . $book->title . '" chưa được phát hành. ';
                    }
                }
            }
        }

        if ($error) {
            return redirect()->route('admin.combos.edit', $combo->id)->with('error_combo', $error);
        }

        if ($request->hasFile('image')) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            if ($combo->public_id) {
                $uploadApi->destroy($combo->public_id);
            }
            $result = $uploadApi->upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'BookStore/Combos'
                ]
            );
            $validated['image'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }


        $combo->update($validated);
        $combo->books()->sync($validated['books']);
        return redirect()->route('admin.combos.index')->with('success_combo', 'Combo đã được cập nhật thành công.');
    }

    public function destroy(Combo $combo)
    {
        $combo->delete();
        return redirect()->route('admin.combos.index')->with('success_combo', 'Combo đã được xóa thành công.');
    }

    public function restore($id)
    {
        $combo = Combo::withTrashed()->findOrFail($id);
        $combo->restore();
        return redirect()->route('admin.combos.index')->with('success_combo', 'Combo đã được khôi phục thành công.');
    }

    public function forceDelete($id)
    {
        $combo = Combo::withTrashed()->findOrFail($id);
        if ($combo->public_id) {
            $cloudinary = new Cloudinary();
            $uploadApi = $cloudinary->uploadApi();
            $uploadApi->destroy($combo->public_id);
        }
        $combo->forceDelete();
        return redirect()->route('admin.combos.index')->with('success_combo', 'Combo đã được xóa vĩnh viễn.');
    }

    // Thêm method mới để xử lý việc chọn/bỏ chọn sách
    public function toggleBook(Request $request)
    {
        $bookId = $request->input('book_id');
        $bookPrice = $request->input('book_price');
        $isSelected = $request->input('is_selected');

        $selectedBooks = session('selected_books', []);

        if ($isSelected) {
            $selectedBooks[$bookId] = $bookPrice;
        } else {
            unset($selectedBooks[$bookId]);
        }

        session(['selected_books' => $selectedBooks]);

        return response()->json([
            'success' => true,
            'selected_books' => $selectedBooks
        ]);
    }
}
