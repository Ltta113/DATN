<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Get the custom messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email không được bỏ trống',
            'email.string' => 'Email phải là một chuỗi',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email không tồn tại trong hệ thống',
            'password.required' => 'Mật khẩu không được bỏ trống',
            'password.string' => 'Mật khẩu phải là một chuỗi',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
        ];
    }
}
