<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'department'      => ['required', 'string', 'max:100'],
            'role'            => ['required', 'string', 'in:admin,hr,manager,employee'],
            'leave_join_date' => ['required', 'date'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }
}
