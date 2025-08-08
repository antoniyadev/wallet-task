<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 *
 * Handles authentication actions such as login and logout.
 * Uses Laravel's session-based authentication with CSRF protection.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Attempt to log in a user with the provided credentials.
     *
     * Validates the user's email/password combination via Laravel's Auth system.
     * Regenerates the session on successful login to prevent session fixation attacks.
     *
     * @param  Request $request HTTP request containing 'email' and 'password'.
     * @return JsonResponse 200 on success, 401 on failure.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Logged in']);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Log out the currently authenticated user.
     *
     * Clears the authentication session and regenerates the CSRF token.
     *
     * @param  Request $request Current HTTP request with active session.
     * @return JsonResponse 200 with a logout confirmation message.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
