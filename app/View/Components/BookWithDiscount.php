<?php

namespace App\View\Components;

use App\Models\Discount;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class BookWithDiscount extends Component
{
    public Discount $discount;
    public LengthAwarePaginator $bookWithDiscount;

    /**
     * Create a new component instance.
     *
     * @param Discount $discount
     * @param LengthAwarePaginator $bookWithDiscount
     *
     * @return void
     */
    public function __construct(Discount $discount, LengthAwarePaginator $bookWithDiscount)
    {
        $this->discount = $discount;
        $this->bookWithDiscount = $bookWithDiscount;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.book-with-discount');
    }
}
