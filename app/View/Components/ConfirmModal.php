<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmModal extends Component
{
    public $id;
    public $title;
    public $message;
    public $confirmText;
    public $cancelText;
    public $formId;
    public $action;

    /**
     * Tạo một instance mới của component với các tham số.
     *
     * @param string $id
     * @param string $title
     * @param string $message
     * @param string $confirmText
     * @param string $cancelText
     * @param string|null $formId
     * @param string $action
     */
    public function __construct(
        $id,
        $title = 'Bạn có chắc không?',
        $message = 'Hành động này không thể hoàn tác!',
        $confirmText = 'Xác nhận',
        $cancelText = 'Hủy',
        $formId = null,
        $action = 'edit'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->confirmText = $confirmText;
        $this->cancelText = $cancelText;
        $this->formId = $formId;
        $this->action = $action;
    }

    /**
     * Lấy view của component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View|Closure|string
    {
        return view('components.confirm-modal');
    }
}
