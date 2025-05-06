<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListOrder extends Component
{
    public $orders;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $orders
    ) {
        $this->orders = $orders;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.list-order');
    }
}
