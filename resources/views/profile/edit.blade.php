@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- ── Profile Information ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Profile Information</h2>
        <p class="text-sm text-gray-500 mb-5">Update your name and email address.</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $user->name) }}"
                       required autofocus autocomplete="name"
                       class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}"
                       required autocomplete="username"
                       class="w-full rounded-lg border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 text-sm text-gray-700">
                        Your email address is unverified.
                        <button form="send-verification"
                                class="underline text-indigo-600 hover:text-indigo-800 ml-1">
                            Re-send verification email
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1 text-green-600 font-medium">
                                A new verification link has been sent to your email.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-1">
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Save Changes
                </button>
                @if (session('status') === 'profile-updated')
                    <p class="text-sm text-green-600">Saved.</p>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Update Password ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Update Password</h2>
        <p class="text-sm text-gray-500 mb-5">Use a long, random password to keep your account secure.</p>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input id="current_password" name="current_password" type="password"
                       autocomplete="current-password"
                       class="w-full rounded-lg border {{ $errors->updatePassword->has('current_password') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @if ($errors->updatePassword->has('current_password'))
                    <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input id="new_password" name="password" type="password"
                       autocomplete="new-password"
                       class="w-full rounded-lg border {{ $errors->updatePassword->has('password') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @if ($errors->updatePassword->has('password'))
                    <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       autocomplete="new-password"
                       class="w-full rounded-lg border {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @if ($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <div class="flex items-center gap-4 pt-1">
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Update Password
                </button>
                @if (session('status') === 'password-updated')
                    <p class="text-sm text-green-600">Password updated.</p>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Delete Account ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Delete Account</h2>
        <p class="text-sm text-gray-500 mb-5">
            Once your account is deleted, all its data will be permanently removed.
            Please download anything you wish to keep before proceeding.
        </p>

        <button @click="open = true"
                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            Delete Account
        </button>

        {{-- Confirmation Modal --}}
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6" @click.outside="open = false">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Are you sure you want to delete your account?</h3>
                <p class="text-sm text-gray-600 mb-5">
                    This action is permanent. Please enter your password to confirm.
                </p>

                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="delete_password" name="password" type="password"
                               placeholder="Enter your password"
                               class="w-full rounded-lg border {{ $errors->userDeletion->has('password') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @if ($errors->userDeletion->has('password'))
                            <p class="mt-1 text-xs text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="open = false"
                                class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
