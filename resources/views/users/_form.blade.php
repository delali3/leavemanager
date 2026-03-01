{{-- User Form Fields --}}
@php $user ??= null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user?->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
               required>
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user?->email ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror"
               required>
        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $user !== null ? '(leave blank to keep)' : '' }} <span class="{{ $user !== null ? '' : 'text-red-500' }}">{{ $user !== null ? '' : '*' }}</span></label>
        <input type="password" name="password"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
               {{ $user !== null ? '' : 'required' }}>
        @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
        <input type="text" name="department" value="{{ old('department', $user?->department ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('department') border-red-400 @enderror"
               placeholder="e.g. Engineering" required>
        @error('department') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select name="role"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('role') border-red-400 @enderror"
                {{ !auth()->user()->hasAnyRole(['admin','hr']) ? 'disabled' : '' }}
                required>
            @foreach($roles as $role)
                <option value="{{ $role->name }}"
                    {{ old('role', $user?->primaryRole ?? '') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Join Date <span class="text-red-500">*</span></label>
        <input type="date" name="leave_join_date" value="{{ old('leave_join_date', $user?->leave_join_date?->format('Y-m-d') ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('leave_join_date') border-red-400 @enderror"
               required>
        @error('leave_join_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 col-span-2">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user?->is_active ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 text-indigo-600 rounded border-gray-300">
            <span class="text-sm text-gray-700">Active Account</span>
        </label>
    </div>
</div>
