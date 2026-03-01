{{-- Leave Type Form Fields (shared between create and edit) --}}

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $leaveType->name ?? '') }}"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
           placeholder="e.g. Annual Leave" required>
    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Max Days Per Year <span class="text-red-500">*</span></label>
    <input type="number" name="max_days" value="{{ old('max_days', $leaveType->max_days ?? '') }}" min="1" max="365"
           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('max_days') border-red-400 @enderror"
           required>
    @error('max_days') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<div class="space-y-3">
    <label class="block text-sm font-medium text-gray-700">Options</label>

    <label class="flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="paid" value="1" {{ old('paid', $leaveType->paid ?? true) ? 'checked' : '' }}
               class="w-4 h-4 text-indigo-600 rounded border-gray-300">
        <span class="text-sm text-gray-700">Paid Leave</span>
    </label>

    <label class="flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="carry_forward" value="1" {{ old('carry_forward', $leaveType->carry_forward ?? false) ? 'checked' : '' }}
               class="w-4 h-4 text-indigo-600 rounded border-gray-300">
        <span class="text-sm text-gray-700">Allow Carry Forward to Next Year</span>
    </label>

    <label class="flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="requires_attachment" value="1" {{ old('requires_attachment', $leaveType->requires_attachment ?? false) ? 'checked' : '' }}
               class="w-4 h-4 text-indigo-600 rounded border-gray-300">
        <span class="text-sm text-gray-700">Requires Attachment (e.g. Medical Certificate)</span>
    </label>
</div>
