@extends('layouts.app')
@section('title', 'Add User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">Create New User</h2>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            @include('users._form')

            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Create User
                </button>
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
