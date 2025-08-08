<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service class responsible for managing User-related operations.
 *
 * Encapsulates the creation, updating, and retrieval of user data,
 * ensuring consistent handling of sensitive information (e.g., password hashing).
 */
class UserService
{
    /**
     * Create a new user.
     *
     * If a password is provided, it will be automatically hashed before saving.
     *
     * @param  array  $data  Associative array of user attributes.
     *                        Expected keys may include:
     *                        - 'name' (string)     : Full name of the user
     *                        - 'email' (string)    : Email address
     *                        - 'password' (string) : Plain text password (will be hashed)
     *                        - other fillable attributes defined in the User model.
     *
     * @return User           The newly created User instance.
     */
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        return User::create($data);
    }

    /**
     * Update an existing user's information.
     *
     * If a password is provided, it will be hashed; if it's empty, it will be excluded from the update.
     *
     * @param  User   $user   The User model instance to update.
     * @param  array  $data   Associative array of updated attributes.
     *                        - 'password' will be hashed if provided and non-empty.
     *                        - if empty, the password will not be changed.
     *
     * @return User           The updated User instance.
     */
    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * Retrieve a paginated list of merchant users.
     *
     * This method assumes the User model defines a `merchants()` scope
     * that filters users with the 'merchant' role.
     *
     * @return LengthAwarePaginator Paginated list of merchants (10 per page).
     */
    public function getMerchants(): LengthAwarePaginator
    {
        return User::merchants()->paginate(10);
    }
}
