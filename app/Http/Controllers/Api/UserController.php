<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * Provides authenticated API users with access to a list of other users
 * (excluding themselves) and their own profile details.
 *
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{
    /**
     * Return a short list of users (excluding the authenticated user).
     *
     * Includes only `id`, `email`, and `name` fields, ordered by email.
     * Intended for selection lists or recipient dropdowns.
     *
     * @param  Request $request Current HTTP request containing the authenticated user.
     * @return JsonResponse JSON array of user records.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->where('id', '!=', $request->user()->id)
            ->select('id', 'email', 'name')
            ->orderBy('email')
            ->get();

        return response()->json($users);
    }

    /**
     * Return full profile details for the authenticated user.
     *
     * Includes the user's id, name, email, role slug, and wallet balance.
     *
     * @param  Request $request Current HTTP request containing the authenticated user.
     * @return JsonResponse JSON object with user profile details.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role->slug,
            'amount' => $user->amount,
        ]);
    }
}
