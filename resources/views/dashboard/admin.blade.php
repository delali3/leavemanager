@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Employees</p>
            <p class="mt-1 text-3xl font-bold text-indigo-600">{{ $stats['total_employees'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Pending Requests</p>
            <p class="mt-1 text-3xl font-bold text-yellow-500">{{ $stats['pending_requests'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Approved This Month</p>
            <p class="mt-1 text-3xl font-bold text-green-500">{{ $stats['approved_this_month'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Leave Types</p>
            <p class="mt-1 text-3xl font-bold text-blue-500">{{ $stats['total_leave_types'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Monthly Chart (simple bar) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Monthly Approvals ({{ now()->year }})</h2>
            <div class="flex items-end gap-2 h-40">
                @php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    $maxVal = max(array_values($monthlyStats) ?: [1]);
                @endphp
                @foreach($months as $i => $month)
                    @php $val = $monthlyStats[$i + 1] ?? 0; $height = $maxVal > 0 ? round(($val / $maxVal) * 100) : 0; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs font-medium text-gray-600">{{ $val ?: '' }}</span>
                        <div class="w-full bg-indigo-{{ $val > 0 ? '500' : '100' }} rounded-t" style="height: {{ max($height, 4) }}%"></div>
                        <span class="text-xs text-gray-400">{{ $month }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-700">Recent Pending Requests</h2>
                <a href="{{ route('leave-requests.index', ['status' => 'pending']) }}" class="text-xs text-indigo-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingRequests as $req)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $req->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $req->leaveType->name }} &bull; {{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M') }} ({{ $req->total_days }}d)</p>
                        </div>
                        <a href="{{ route('leave-requests.show', $req) }}" class="text-xs text-indigo-600 hover:underline">Review</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No pending requests.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white rounded-xl p-4 text-center hover:bg-indigo-700 transition">
            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span class="text-sm font-medium">Add User</span>
        </a>
        <a href="{{ route('leave-types.create') }}" class="bg-green-600 text-white rounded-xl p-4 text-center hover:bg-green-700 transition">
            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="text-sm font-medium">New Leave Type</span>
        </a>
        <a href="{{ route('reports.index') }}" class="bg-blue-600 text-white rounded-xl p-4 text-center hover:bg-blue-700 transition">
            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="text-sm font-medium">Reports</span>
        </a>
        <a href="{{ route('leave-requests.index') }}" class="bg-yellow-500 text-white rounded-xl p-4 text-center hover:bg-yellow-600 transition">
            <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-sm font-medium">All Requests</span>
        </a>
    </div>

</div>
@endsection
