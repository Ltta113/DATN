<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
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
}
