@extends('layouts.app')
@section('title', 'Leave Types')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Manage all leave types available in the system.</p>
        @can('create', App\Models\LeaveType::class)
            <a href="{{ route('leave-types.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Leave Type
            </a>
        @endcan
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-6 py-3">Name</th>
                    <th class="text-center px-4 py-3">Max Days</th>
                    <th class="text-center px-4 py-3">Paid</th>
                    <th class="text-center px-4 py-3">Carry Forward</th>
                    <th class="text-center px-4 py-3">Requires Attachment</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leaveTypes as $type)
                    <tr class="{{ $type->trashed() ? 'opacity-50 bg-gray-50' : '' }}">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $type->name }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ $type->max_days }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($type->paid) <span class="text-green-600">&#10003;</span> @else <span class="text-red-400">&#10007;</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($type->carry_forward) <span class="text-green-600">&#10003;</span> @else <span class="text-red-400">&#10007;</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($type->requires_attachment) <span class="text-green-600">&#10003;</span> @else <span class="text-red-400">&#10007;</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($type->trashed())
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Deleted</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right flex items-center justify-end gap-2">
                            @if($type->trashed())
                                @can('restore', $type)
                                    <form method="POST" action="{{ route('leave-types.restore', $type->id) }}">
                                        @csrf
                                        <button class="text-xs text-green-600 hover:underline">Restore</button>
                                    </form>
                                @endcan
                            @else
                                @can('update', $type)
                                    <a href="{{ route('leave-types.edit', $type) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                                @endcan
                                @can('delete', $type)
                                    <form method="POST" action="{{ route('leave-types.destroy', $type) }}" onsubmit="return confirm('Delete this leave type?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">No leave types found. Create one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $leaveTypes->links() }}</div>
</div>
@endsection
