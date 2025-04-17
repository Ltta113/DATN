<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the authentication page of the provider.
     *
     * @param string $provider
     *
     * @return RedirectResponse|JsonResponse
     */
    public function redirectToProvider(string $provider): RedirectResponse|JsonResponse
    {
        // Validate the provider
        if (!in_array($provider, ['google', 'facebook'])) {
            return response()->json(['error' => 'Provider not supported'], 400);
        }

        return Response::json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param string $provider
     *
     * @return JsonResponse
     */
    public function callbackProvider(string $provider): RedirectResponse|JsonResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::firstOrNew(['email' => $socialUser->getEmail()]);

            if (!$user->full_name) {
                $user->full_name = $socialUser->getName();
            }

            if (!$user->avatar) {
                $user->avatar = $socialUser->getAvatar();
            }

            $user->provider = $provider;
            $user->provider_id = $socialUser->getId();

            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
