<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year   = $request->input('year', now()->year);
        $month  = $request->input('month');
        $status = $request->input('status');

        $query = LeaveRequest::with(['user', 'leaveType', 'approver'])
            ->whereYear('start_date', $year);

        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->user()->hasRole('manager') && !$request->user()->hasAnyRole(['admin', 'hr'])) {
            // Managers see all requests (adjust if you need department-based filtering)
        }

        $requests = $query->latest()->paginate(30)->withQueryString();

        $summary = [
            'total'    => $query->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'pending'  => (clone $query)->where('status', 'pending')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $leaveTypes  = LeaveType::orderBy('name')->get();
        $years       = range(now()->year - 2, now()->year + 1);
        $departments = User::distinct()->pluck('department')->filter()->sort()->values();

        return view('reports.index', compact(
            'requests', 'summary', 'leaveTypes', 'years', 'departments', 'year', 'month', 'status'
        ));
    }
}
