<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();

        return $user && optional($user->role)->slug === 'admin';
    }

    public function rules(): array
    {
        return [
            'user_id'     => ['required', 'exists:users,id'],
            'amount'      => ['required', 'integer', 'min:1'],
            'type'        => ['required', 'in:credit,debit'],
            'description' => ['required', 'string', 'max:255'],
        ];
    }
}
