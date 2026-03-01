<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('leave_type'));
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:100', Rule::unique('leave_types', 'name')->ignore($this->route('leave_type'))->whereNull('deleted_at')],
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
