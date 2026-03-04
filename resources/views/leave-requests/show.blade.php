@extends('layouts.app')
@section('title', 'Leave Request Details')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Request Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $leaveRequest->leaveType->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Submitted {{ $leaveRequest->created_at->diffForHumans() }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                {{ $leaveRequest->isApproved() ? 'bg-green-100 text-green-700' : ($leaveRequest->isRejected() ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($leaveRequest->status) }}
            </span>
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
            @hasanyrole('admin|hr|manager')
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</dt>
                <dd class="mt-1 text-gray-800 font-medium">{{ $leaveRequest->user->name }}</dd>
                <dd class="text-xs text-gray-400">{{ $leaveRequest->user->department }}</dd>
            </div>
            @endhasanyrole

            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</dt>
                <dd class="mt-1 text-gray-800">{{ $leaveRequest->start_date->format('D, d M Y') }}</dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</dt>
                <dd class="mt-1 text-gray-800">{{ $leaveRequest->end_date->format('D, d M Y') }}</dd>
            </div>

            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Working Days</dt>
                <dd class="mt-1 text-gray-800 font-bold text-lg">{{ $leaveRequest->total_days }}</dd>
            </div>

            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</dt>
                <dd class="mt-1 text-gray-700">{{ $leaveRequest->reason }}</dd>
            </div>

            @if($leaveRequest->attachment)
            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Attachment</dt>
                <dd class="mt-1">
                    <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank"
                       class="text-indigo-600 hover:underline flex items-center gap-1 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        View Attachment
                    </a>
                </dd>
            </div>
            @endif

            @if($leaveRequest->approved_by)
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed By</dt>
                <dd class="mt-1 text-gray-800">{{ $leaveRequest->approver->name }}</dd>
                <dd class="text-xs text-gray-400">{{ $leaveRequest->approved_at?->format('d M Y, H:i') }}</dd>
            </div>
            @endif

            @if($leaveRequest->manager_comment)
            <div class="col-span-2">
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">HR Comment</dt>
                <dd class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-700 text-sm italic">{{ $leaveRequest->manager_comment }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Print Leave Letter (approved requests only) --}}
    @if($leaveRequest->isApproved())
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-green-800">Leave approved</p>
            <p class="text-xs text-green-600 mt-0.5">Your leave letter is ready to print.</p>
        </div>
        <a href="{{ route('leave-requests.letter', $leaveRequest) }}"
           target="_blank"
           class="flex items-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Leave Letter
        </a>
    </div>
    @endif

    {{-- Approve / Reject Form — HR and Admin only --}}
    @hasanyrole('admin|hr')
    @can('approve', $leaveRequest)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Review This Request</h3>

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Comment (optional)</label>
            <textarea name="manager_comment" rows="2" id="reviewComment"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="Add a comment for the employee..."></textarea>
        </div>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}" id="approveForm">
                @csrf
                <input type="hidden" name="manager_comment" id="approveComment">
                <button type="submit"
                        onclick="document.getElementById('approveComment').value = document.getElementById('reviewComment').value; return confirm('Approve this leave request?')"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-green-700">
                    ✓ Approve
                </button>
            </form>

            <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}" id="rejectForm">
                @csrf
                <input type="hidden" name="manager_comment" id="rejectComment">
                <button type="submit"
                        onclick="document.getElementById('rejectComment').value = document.getElementById('reviewComment').value; return confirm('Reject this leave request?')"
                        class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-red-700">
                    ✗ Reject
                </button>
            </form>
        </div>
    </div>
    @endcan
    @endhasanyrole

    {{-- Delete Button --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to requests</a>

        @can('delete', $leaveRequest)
            <form method="POST" action="{{ route('leave-requests.destroy', $leaveRequest) }}"
                  onsubmit="return confirm('Delete this leave request permanently?')" class="ml-auto">
                @csrf @method('DELETE')
                <button class="text-xs text-red-600 hover:underline">Delete Request</button>
            </form>
        @endcan
    </div>

</div>
@endsection
