@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Leave Balance Cards --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-700">My Leave Balances ({{ now()->year }})</h2>
            <a href="{{ route('leave-requests.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Request Leave
            </a>
        </div>

        @if($balances->isEmpty())
            <div class="bg-white rounded-xl p-8 text-center border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-sm">No leave balances set up. Please contact HR.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($balances as $balance)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-700">{{ $balance->leaveType->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $balance->leaveType->paid ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $balance->leaveType->paid ? 'Paid' : 'Unpaid' }}
                            </span>
                        </div>
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Used: {{ $balance->used_days }}</span>
                                <span>Total: {{ $balance->total_days }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                @php
                                    $pct = $balance->total_days > 0 ? round(($balance->used_days / $balance->total_days) * 100) : 0;
                                    $color = $pct >= 80 ? 'bg-red-500' : ($pct >= 50 ? 'bg-yellow-400' : 'bg-indigo-500');
                                @endphp
                                <div class="h-2 rounded-full {{ $color }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <p class="mt-3 text-2xl font-bold {{ $balance->remaining_days > 0 ? 'text-indigo-600' : 'text-red-500' }}">
                            {{ $balance->remaining_days }}
                            <span class="text-sm font-normal text-gray-400">days left</span>
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Requests --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-700">My Recent Requests</h2>
            <a href="{{ route('leave-requests.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="pb-2">Type</th>
                        <th class="pb-2">Dates</th>
                        <th class="pb-2">Days</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentRequests as $req)
                        <tr>
                            <td class="py-2 font-medium text-gray-800">{{ $req->leaveType->name }}</td>
                            <td class="py-2 text-gray-500">{{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M Y') }}</td>
                            <td class="py-2 text-gray-500">{{ $req->total_days }}</td>
                            <td class="py-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $req->isApproved() ? 'bg-green-100 text-green-700' : ($req->isRejected() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="py-2 text-right">
                                <a href="{{ route('leave-requests.show', $req) }}" class="text-indigo-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400">You haven't submitted any leave requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
