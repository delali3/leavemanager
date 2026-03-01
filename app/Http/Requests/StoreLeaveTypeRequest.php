<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\LeaveType::class);
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:100', 'unique:leave_types,name'],
            'max_days'            => ['required', 'integer', 'min:1', 'max:365'],
            'paid'                => ['sometimes', 'boolean'],
            'carry_forward'       => ['sometimes', 'boolean'],
            'requires_attachment' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'paid'                => $this->boolean('paid'),
            'carry_forward'       => $this->boolean('carry_forward'),
            'requires_attachment' => $this->boolean('requires_attachment'),
        ]);
    }
}
