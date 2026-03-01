<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', LeaveType::class);

        $leaveTypes = LeaveType::withTrashed()
            ->orderBy('name')
            ->paginate(15);

        return view('leave-types.index', compact('leaveTypes'));
    }

    public function create(): View
    {
        $this->authorize('create', LeaveType::class);

        return view('leave-types.create');
    }

    public function store(StoreLeaveTypeRequest $request): RedirectResponse
    {
        LeaveType::create($request->validated());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType): View
    {
        $this->authorize('view', $leaveType);

        return view('leave-types.show', compact('leaveType'));
    }

    public function edit(LeaveType $leaveType): View
    {
        $this->authorize('update', $leaveType);

        return view('leave-types.edit', compact('leaveType'));
    }

    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        $leaveType->update($request->validated());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        $this->authorize('delete', $leaveType);

        $leaveType->delete();

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $leaveType = LeaveType::withTrashed()->findOrFail($id);
        $this->authorize('restore', $leaveType);

        $leaveType->restore();

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type restored successfully.');
    }
}
