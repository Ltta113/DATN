<?php

namespace App\View\Components;

use App\Models\Book;
use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListBookRecentOrder extends Component
{
    public $books;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->books = Book::orderBy('sold', 'desc')->limit(5)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.list-book-recent-order');
    }
}
