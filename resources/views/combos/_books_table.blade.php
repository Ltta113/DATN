@php
    $selectedBooks = old('books', []);
@endphp

@foreach ($books as $book)
    <tr class="hover:bg-gray-50">
        <td class="py-3 px-4">
            <input type="checkbox" name="books[]" value="{{ $book->id }}"
                class="book-checkbox rounded" data-price="{{ $book->price }}"
                {{ isset($selectedBooks[$book->id]) ? 'checked' : '' }}>
        </td>
        <td class="py-3 px-4">
            <div class="font-medium text-gray-900">{{ $book->title }}</div>
        </td>
        <td class="py-3 px-4">
            <div class="text-sm text-gray-900">
                {{ number_format($book->price, 0, ',', '.') }} ₫
            </div>
        </td>
        <td class="py-3 px-4">
            <div class="text-sm {{ $book->stock > 10 ? 'text-green-600' : ($book->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $book->stock }}
            </div>
        </td>
    </tr>
@endforeach

@if($books->hasPages())
    <tr>
        <td colspan="4" class="py-3 px-4">
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        </td>
    </tr>
@endif
