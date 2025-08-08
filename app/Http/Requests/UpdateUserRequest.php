<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $routeUser = $this->route('user');          // can be id or model
        $userId    = is_object($routeUser) ? $routeUser->id : $routeUser;

        return [
            'name'        => ['sometimes','required','string','max:255'],
            'email'       => ['sometimes','required','email','max:255', Rule::unique('users', 'email')->ignore($userId)],
            'description' => ['sometimes','nullable','string','max:1000'], // <-- fixed key
            'password'    => ['sometimes','nullable','string','min:6'],
            'role_id'     => ['sometimes','required','exists:roles,id'],
            'amount'      => ['sometimes','integer','min:0'],
        ];
    }
}
