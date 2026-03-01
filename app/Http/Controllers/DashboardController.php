<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveBalanceService;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly LeaveBalanceService $balanceService,
        private readonly LeaveRequestService $requestService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin') || $user->hasRole('hr')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('manager')) {
            return $this->managerDashboard($user);
        }

        return $this->employeeDashboard($user);
    }

    private function adminDashboard()
    {
        $stats = [
            'total_employees'    => User::where('is_active', true)->count(),
            'pending_requests'   => LeaveRequest::pending()->count(),
            'approved_this_month' => LeaveRequest::approved()->thisMonth()->count(),
            'total_leave_types'  => \App\Models\LeaveType::count(),
        ];

        $monthlyStats    = $this->requestService->getMonthlyStats();
        $pendingRequests = LeaveRequest::with(['user', 'leaveType'])
            ->pending()
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'monthlyStats', 'pendingRequests'));
    }

    private function managerDashboard(User $user)
    {
        $pendingRequests = LeaveRequest::with(['user', 'leaveType'])
            ->pending()
            ->latest()
            ->take(20)
            ->get();

        $recentDecisions = LeaveRequest::with(['user', 'leaveType'])
            ->where('approved_by', $user->id)
            ->latest('approved_at')
            ->take(10)
            ->get();

        return view('dashboard.manager', compact('pendingRequests', 'recentDecisions'));
    }

    private function employeeDashboard(User $user)
    {
        $balances = $this->balanceService->getForUser($user);

        $recentRequests = LeaveRequest::with('leaveType')
            ->forUser($user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.employee', compact('balances', 'recentRequests'));
    }
}
