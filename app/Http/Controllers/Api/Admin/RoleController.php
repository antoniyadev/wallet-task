<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

/**
 * Class RoleController
 *
 * Handles retrieval of user roles for admin usage.
 * Provides a list of available roles with minimal fields for dropdowns or selection inputs.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class RoleController extends Controller
{
    /**
     * Get a list of available roles.
     *
     * Returns only `id` and `name` fields for each role.
     *
     * @return JsonResponse List of roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::select('id', 'name')->get();

        return response()->json($roles);
    }
}
