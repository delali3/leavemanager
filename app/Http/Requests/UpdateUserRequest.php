<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password'        => ['nullable', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'department'      => ['required', 'string', 'max:100'],
            'role'            => ['sometimes', 'string', 'in:admin,hr,manager,employee'],
            'leave_join_date' => ['required', 'date'],
            'is_active'       => ['sometimes', 'boolean'],
        ];
    }
}
