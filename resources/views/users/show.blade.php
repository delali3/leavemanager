@extends('layouts.app')
@section('title', $user->name)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-2xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <span class="mt-1 inline-block text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full capitalize">
                        {{ $user->primaryRole }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $user)
                    <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700">Edit</a>
                @endcan
                <span class="text-xs px-2 py-1 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Department</p>
                <p class="mt-0.5 font-medium text-gray-800">{{ $user->department ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Leave Join Date</p>
                <p class="mt-0.5 font-medium text-gray-800">{{ $user->leave_join_date?->format('d M Y') ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Leave Balances --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Leave Balances ({{ now()->year }})</h3>
        @if($balances->isEmpty())
            <p class="text-sm text-gray-400">No leave balances configured.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($balances as $balance)
                    <div class="p-3 border border-gray-100 rounded-lg">
                        <p class="text-xs font-semibold text-gray-600">{{ $balance->leaveType->name }}</p>
                        <div class="mt-2 flex justify-between text-xs text-gray-500">
                            <span>Used: {{ $balance->used_days }}</span>
                            <span>Total: {{ $balance->total_days }}</span>
                        </div>
                        <p class="mt-1 text-xl font-bold {{ $balance->remaining_days > 0 ? 'text-indigo-600' : 'text-red-500' }}">
                            {{ $balance->remaining_days }} <span class="text-xs font-normal text-gray-400">left</span>
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Requests --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Recent Leave Requests</h3>
            <a href="{{ route('leave-requests.index') }}?user_id={{ $user->id }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="space-y-2">
            @forelse($recentRequests as $req)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $req->leaveType->name }}</p>
                        <p class="text-xs text-gray-400">{{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M Y') }} &bull; {{ $req->total_days }} day(s)</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                        {{ $req->isApproved() ? 'bg-green-100 text-green-700' : ($req->isRejected() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-3">No leave requests.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
