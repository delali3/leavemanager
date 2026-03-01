@extends('layouts.app')
@section('title', 'Edit Leave Type')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Edit: {{ $leaveType->name }}</h2>

        <form method="POST" action="{{ route('leave-types.update', $leaveType) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('leave-types._form')

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Update Leave Type
                </button>
                <a href="{{ route('leave-types.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
