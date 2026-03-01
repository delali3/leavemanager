@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Year</label>
                <select name="year" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Month</label>
                <select name="month" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    <option value="">All Months</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700">Generate</button>
            <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $summary['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-yellow-500">{{ $summary['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500">Rejected</p>
            <p class="text-2xl font-bold text-red-500">{{ $summary['rejected'] }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Leave Request Report — {{ $year }}{{ $month ? ' / ' . date('F', mktime(0,0,0,$month,1)) : '' }}</h2>
            <span class="text-xs text-gray-400">{{ $requests->total() }} records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-6 py-3">Employee</th>
                        <th class="text-left px-4 py-3">Dept</th>
                        <th class="text-left px-4 py-3">Leave Type</th>
                        <th class="text-left px-4 py-3">From</th>
                        <th class="text-left px-4 py-3">To</th>
                        <th class="text-center px-4 py-3">Days</th>
                        <th class="text-center px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Reviewed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-2 font-medium text-gray-800">{{ $req->user->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $req->user->department ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $req->leaveType->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $req->start_date->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $req->end_date->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-center font-semibold text-gray-700">{{ $req->total_days }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $req->isApproved() ? 'bg-green-100 text-green-700' : ($req->isRejected() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $req->approver?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400">No data for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $requests->withQueryString()->links() }}</div>
    </div>

</div>
@endsection
