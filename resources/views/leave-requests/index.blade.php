@extends('layouts.app')
@section('title', 'Leave Requests')

@section('content')
<div class="space-y-4">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Leave Type</label>
                <select name="leave_type_id" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700">Filter</button>
            <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>

            @can('create', App\Models\LeaveRequest::class)
                <div class="ml-auto">
                    <a href="{{ route('leave-requests.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Request
                    </a>
                </div>
            @endcan
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    @hasanyrole('admin|hr|manager')
                    <th class="text-left px-6 py-3">Employee</th>
                    @endhasanyrole
                    <th class="text-left px-6 py-3">Leave Type</th>
                    <th class="text-left px-4 py-3">Dates</th>
                    <th class="text-center px-4 py-3">Days</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-right px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50">
                        @hasanyrole('admin|hr|manager')
                        <td class="px-6 py-3">
                            <div>
                                <p class="font-medium text-gray-800">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $req->user->department }}</p>
                            </div>
                        </td>
                        @endhasanyrole
                        <td class="px-6 py-3 font-medium text-gray-700">{{ $req->leaveType->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $req->start_date->format('d M Y') }}<br>→ {{ $req->end_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $req->total_days }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $req->isApproved() ? 'bg-green-100 text-green-700' : ($req->isRejected() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('leave-requests.show', $req) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">No leave requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>
</div>
@endsection
