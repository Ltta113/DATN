<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUserInfo(Request $request)
    {
        $user = User::with(['bookmarks'])
            ->where('id', $request->user()->id)
            ->first();

        return response()->json([
            'message' => "Thông tin người dùng",
            'data' => new UserResource($user),
        ]);
    }

    private const VALIDATION_STRING_MAX_255 = 'required|string|max:255';

    public function updateUserInfo(Request $request)
    {
        $user = User::find($request->user()->id);
        $valiated = $request->validate([
            'full_name' => self::VALIDATION_STRING_MAX_255,
            'phone_number' => 'required|digits:10|starts_with:0',
            'birth_day' => 'required|date',
            'address' => self::VALIDATION_STRING_MAX_255,
            'province' => self::VALIDATION_STRING_MAX_255,
            'district' => self::VALIDATION_STRING_MAX_255,
            'ward' => self::VALIDATION_STRING_MAX_255,
        ], [
            'full_name.required' => 'Tên không được để trống',
            'phone_number.required' => 'Số điện thoại không được để trống',
            'birth_day.required' => 'Ngày sinh không được để trống',
            'address.required' => 'Địa chỉ không được để trống',
            'province.required' => 'Tỉnh/Thành phố không được để trống',
            'district.required' => 'Quận/Huyện không được để trống',
            'ward.required' => 'Phường/Xã không được để trống',
            'full_name.string' => 'Tên không hợp lệ',
            'birth_day.date' => 'Ngày sinh không hợp lệ',
            'address.string' => 'Địa chỉ không hợp lệ',
            'province.string' => 'Tỉnh/Thành phố không hợp lệ',
            'district.string' => 'Quận/Huyện không hợp lệ',
            'ward.string' => 'Phường/Xã không hợp lệ',
            'full_name.max' => 'Tên không được quá 255 ký tự',
            'address.max' => 'Địa chỉ không được quá 255 ký tự',
            'province.max' => 'Tỉnh/Thành phố không được quá 255 ký tự',
            'district.max' => 'Quận/Huyện không được quá 255 ký tự',
            'ward.max' => 'Phường/Xã không được quá 255 ký tự',
            'phone_number.min' => 'Số điện thoại phải có 10 ký tự',
            'phone_number.max' => 'Số điện thoại phải có 10 ký tự',
            'phone_number.starts_with' => 'Số điện thoại phải bắt đầu bằng số 0',
            'phone_number.numeric' => 'Số điện thoại phải là số',
        ]);
        $user->update($valiated);

        return response()->json([
            'message' => "Cập nhật thông tin người dùng thành công",
            'data' => new UserResource($user),
        ]);
    }

    public function avatarManager(Request $request)
    {
        $user = User::find($request->user()->id);
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Ảnh đại diện không được để trống',
            'avatar.image' => 'Ảnh đại diện không hợp lệ',
            'avatar.mimes' => 'Ảnh đại diện không hợp lệ',
            'avatar.max' => 'Ảnh đại diện không được quá 2MB',
        ]);
        if ($request->hasFile('avatar')) {
            $cloudinary = new Cloudinary();
            if ($user->public_id) {
                $cloudinary->uploadApi()->destroy($user->public_id);
            }
            $result = $cloudinary->uploadApi()->upload(
                $request->file('avatar')->getRealPath(),
                [
                    'folder' => 'BookStore/Users',
                ]
            );

            $validated['avatar'] = $result['secure_url'];
            $validated['public_id'] = $result['public_id'];
        }
        $user->update($validated);

        return response()->json([
            'message' => "Cập nhật ảnh đại diện thành công",
            'data' => new UserResource($user),
        ]);
    }
}
