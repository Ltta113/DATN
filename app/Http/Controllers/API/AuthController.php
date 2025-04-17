<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     *
     * @return \JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login the user.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không hợp lệ',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout the user (Invalidate the token).
     *
     * @param Request $request
     *
     * @return \JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công',
        ]);
    }
    /**
     * Refresh the token.
     *
     * @param Request $request
     *
     * @return \JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
        ]);
    }
    /**
     * Get the authenticated user's profile.
     *
     * @param Request $request
     *
     * @return \JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     *
     * @return \JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->only(['full_name', 'phone_number', 'address', 'profile_picture']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }

}
