<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'hr']);
    }

    public function rules(): array
    {
        return [
            'manager_comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}
