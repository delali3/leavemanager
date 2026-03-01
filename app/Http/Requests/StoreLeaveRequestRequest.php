<?php

namespace App\Http\Requests;

use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\LeaveRequest::class);
    }

    public function rules(): array
    {
        return [
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date'    => ['required', 'date', 'after_or_equal:today'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'reason'        => ['required', 'string', 'min:10', 'max:1000'],
            'attachment'    => $this->attachmentRules(),
        ];
    }

    private function attachmentRules(): array
    {
        $leaveType = LeaveType::find($this->input('leave_type_id'));

        $rules = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];

        if ($leaveType && $leaveType->requires_attachment) {
            $rules[0] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Leave start date cannot be in the past.',
            'end_date.after_or_equal'   => 'End date must be on or after the start date.',
            'attachment.required'        => 'An attachment is required for this leave type.',
            'attachment.max'             => 'The attachment must not exceed 2MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $start = $this->date('start_date');
            $end   = $this->date('end_date');

            if ($start && $end && $start->isWeekend() && $end->isWeekend() && $start->eq($end)) {
                $validator->errors()->add('start_date', 'Cannot request leave on weekends only.');
            }
        });
    }
}
