<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->id,
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'email.max' => 'Email không được vượt quá 255 ký tự',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu không được bỏ trống',
            'password.string' => 'Mật khẩu phải là một chuỗi',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'full_name.string' => 'Họ tên phải là một chuỗi',
            'full_name.max' => 'Họ tên không được vượt quá 255 ký tự',
            'phone_number.string' => 'Số điện thoại phải là một chuỗi',
            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'address.string' => 'Địa chỉ phải là một chuỗi',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'profile_picture.image' => 'Ảnh đại diện phải là một hình ảnh',
            'profile_picture.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg hoặc gif',
            'profile_picture.max' => 'Ảnh đại diện không được vượt quá 2MB',
        ];
    }
}
