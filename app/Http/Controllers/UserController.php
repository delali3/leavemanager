<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private readonly LeaveBalanceService $balanceService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('roles');

        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        if ($role = $request->input('role')) {
            $query->role($role);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users       = $query->latest()->paginate(20)->withQueryString();
        $roles       = Role::orderBy('name')->get();
        $departments = User::distinct()->pluck('department')->filter()->sort()->values();

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data     = $request->validated();
        $roleName = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        $user = User::create($data);
        $user->assignRole($roleName);

        // Initialize leave balances for current year
        $this->balanceService->initializeForUser($user);

        return redirect()->route('users.show', $user)
            ->with('success', 'User created and leave balances initialized.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('roles');
        $balances = $this->balanceService->getForUser($user);
        $recentRequests = $user->leaveRequests()
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get();

        return view('users.show', compact('user', 'balances', 'recentRequests'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['role']) && $request->user()->hasAnyRole(['admin', 'hr'])) {
            $user->syncRoles([$data['role']]);
        }
        unset($data['role'], $data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->update(['is_active' => false]);

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully.');
    }
}
