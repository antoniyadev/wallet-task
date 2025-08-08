<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

/**
 * Class UserController
 *
 * Admin API for managing merchant users.
 * Exposes CRUD endpoints and delegates business logic to the UserService.
 */
class UserController extends Controller
{
    /**
     * Service layer for user-related operations.
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Bind resource authorization and inject the user service.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->authorizeResource(User::class, 'user');
        $this->userService = $userService;
    }

    /**
     * List merchant users.
     *
     * @return JsonResponse JSON array (or paginator payload) of merchants.
     */
    public function index(): JsonResponse
    {
        $merchants = $this->userService->getMerchants();

        return response()->json($merchants);
    }

    /**
     * Create a new user.
     *
     * @param  StoreUserRequest $request Validated request payload.
     * @return JsonResponse Created user payload on success; 500 on failure.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->validated());

            return response()->json($user, 201);
        } catch (Throwable $e) {
            return response()->json(['message' => 'User creation failed.'], 500);
        }
    }

    /**
     * Show a single user with transactions.
     *
     * @param  User $user Route-model-bound user.
     * @return JsonResponse User with transactions relation loaded.
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['transactions' => function ($q) {
            $q->latest();
        }]);

        return response()->json($user);
    }

    /**
     * Update a user.
     *
     * @param  UpdateUserRequest $request Validated request payload.
     * @param  User              $user    Target user (route-model-bound).
     * @return JsonResponse Updated user payload on success; 500 on failure.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $updatedUser = $this->userService->update($user, $request->validated());

            return response()->json($updatedUser);
        } catch (Throwable $e) {
            return response()->json(['message' => 'User update failed.'], 500);
        }
    }

    /**
     * Delete a user.
     *
     * @param  User $user Target user (route-model-bound).
     * @return Response 204 No Content on success.
     */
    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
