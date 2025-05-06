<?php

namespace App\View\Components;

use App\Models\Discount;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class BookWithoutDiscount extends Component
{
    public Discount $discount;
    public LengthAwarePaginator $bookWithoutDiscount;

    /**
     * Create a new component instance.
     *
     * @param Discount $discount
     * @param LengthAwarePaginator $bookWithoutDiscount
     *
     * @return void
     */
    public function __construct(Discount $discount, LengthAwarePaginator $bookWithoutDiscount)
    {
        $this->discount = $discount;
        $this->bookWithoutDiscount = $bookWithoutDiscount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.book-without-discount');
    }
}
