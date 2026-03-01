@extends('layouts.app')
@section('title', $leaveType->name)

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-gray-700">{{ $leaveType->name }}</h2>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $leaveType->trashed() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ $leaveType->trashed() ? 'Deleted' : 'Active' }}
            </span>
        </div>

        <dl class="space-y-4 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-50">
                <dt class="text-gray-500">Max Days Per Year</dt>
                <dd class="font-semibold text-gray-800">{{ $leaveType->max_days }} days</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <dt class="text-gray-500">Type</dt>
                <dd class="font-semibold {{ $leaveType->paid ? 'text-green-600' : 'text-gray-600' }}">
                    {{ $leaveType->paid ? 'Paid' : 'Unpaid' }}
                </dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <dt class="text-gray-500">Carry Forward</dt>
                <dd>{{ $leaveType->carry_forward ? 'Yes' : 'No' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-50">
                <dt class="text-gray-500">Requires Attachment</dt>
                <dd>{{ $leaveType->requires_attachment ? 'Yes (e.g. medical cert)' : 'No' }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-gray-500">Created</dt>
                <dd class="text-gray-600">{{ $leaveType->created_at->format('d M Y') }}</dd>
            </div>
        </dl>

        <div class="flex items-center gap-3 mt-6 pt-4 border-t border-gray-100">
            @can('update', $leaveType)
                <a href="{{ route('leave-types.edit', $leaveType) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Edit</a>
            @endcan
            <a href="{{ route('leave-types.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back</a>
        </div>
    </div>
</div>
@endsection
