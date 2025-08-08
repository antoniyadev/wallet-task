<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->authorizeResource(User::class, 'user');

        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        $merchants = $this->userService->getMerchants();

        return response()->json($merchants);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());
            return response()->json($user, 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'User creation failed.'], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        $user->load('transactions');

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $updatedUser = $this->userService->update($user, $request->validated());
            return response()->json($updatedUser);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'User update failed.'], 500);
        }

    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
