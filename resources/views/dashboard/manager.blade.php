@extends('layouts.app')
@section('title', 'Manager Dashboard')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pending Requests for Approval --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-700">Pending Approvals</h2>
                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($pendingRequests as $req)
                    <div class="p-3 border border-gray-100 rounded-lg hover:border-indigo-200 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $req->leaveType->name }} &mdash; {{ $req->total_days }} day(s)</p>
                                <p class="text-xs text-gray-400">{{ $req->start_date->format('d M Y') }} to {{ $req->end_date->format('d M Y') }}</p>
                            </div>
                            <a href="{{ route('leave-requests.show', $req) }}" class="text-xs bg-indigo-600 text-white px-3 py-1 rounded-lg hover:bg-indigo-700">Review</a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        All caught up! No pending requests.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Decisions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">My Recent Decisions</h2>
            <div class="space-y-3">
                @forelse($recentDecisions as $req)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $req->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->leaveType->name }} &bull; {{ $req->approved_at?->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $req->isApproved() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No recent decisions.</p>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('leave-requests.index') }}" class="text-sm text-indigo-600 hover:underline">View all requests &rarr;</a>
            </div>
        </div>

    </div>

</div>
@endsection
