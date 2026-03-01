<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveLeaveRequestRequest;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(private readonly LeaveRequestService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $user  = $request->user();
        $query = LeaveRequest::with(['user', 'leaveType', 'approver']);

        // Employees only see their own requests
        if ($user->hasRole('employee')) {
            $query->forUser($user->id);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by leave type
        if ($leaveTypeId = $request->input('leave_type_id')) {
            $query->where('leave_type_id', $leaveTypeId);
        }

        $requests   = $query->latest()->paginate(15)->withQueryString();
        $leaveTypes = LeaveType::orderBy('name')->get();

        return view('leave-requests.index', compact('requests', 'leaveTypes'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', LeaveRequest::class);

        $leaveTypes = LeaveType::orderBy('name')->get();
        $user       = $request->user();
        $balances   = $user->leaveBalances()
            ->with('leaveType')
            ->where('year', now()->year)
            ->get()
            ->keyBy('leave_type_id');

        return view('leave-requests.create', compact('leaveTypes', 'balances'));
    }

    public function store(StoreLeaveRequestRequest $request): RedirectResponse
    {
        try {
            $leaveRequest = $this->service->create(
                $request->user(),
                $request->validated(),
                $request->file('attachment')
            );

            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('success', 'Leave request submitted successfully. You will be notified once reviewed.');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(LeaveRequest $leaveRequest): View
    {
        $this->authorize('view', $leaveRequest);

        $leaveRequest->load(['user', 'leaveType', 'approver']);

        return view('leave-requests.show', compact('leaveRequest'));
    }

    public function approve(ApproveLeaveRequestRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('approve', $leaveRequest);

        try {
            $this->service->approve(
                $leaveRequest,
                $request->user(),
                $request->input('manager_comment')
            );

            return back()->with('success', 'Leave request approved successfully.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(ApproveLeaveRequestRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('reject', $leaveRequest);

        try {
            $this->service->reject(
                $leaveRequest,
                $request->user(),
                $request->input('manager_comment')
            );

            return back()->with('success', 'Leave request rejected.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorize('delete', $leaveRequest);

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request deleted.');
    }
}
