@extends('layouts.app')
@section('title', 'Users')

@section('content')
<div class="space-y-4">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                       class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Role</label>
                <select name="role" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Department</label>
                <select name="department" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700">Filter</button>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>

            @can('create', App\Models\User::class)
                <div class="ml-auto">
                    <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Add User
                    </a>
                </div>
            @endcan
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-6 py-3">Name</th>
                    <th class="text-left px-4 py-3">Department</th>
                    <th class="text-center px-4 py-3">Role</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Join Date</th>
                    <th class="text-right px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->department ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full capitalize">
                                {{ $user->primaryRole }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->leave_join_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-6 py-3 text-right flex items-center justify-end gap-3">
                            <a href="{{ route('users.show', $user) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="text-xs text-gray-600 hover:underline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
