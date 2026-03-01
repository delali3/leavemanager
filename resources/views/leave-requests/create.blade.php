@extends('layouts.app')
@section('title', 'Submit Leave Request')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-5">New Leave Request</h2>

        <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data" class="space-y-4" id="leaveForm">
            @csrf

            {{-- Leave Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                <select name="leave_type_id" id="leaveTypeSelect"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('leave_type_id') border-red-400 @enderror"
                        required>
                    <option value="">— Select Leave Type —</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}"
                                data-requires-attachment="{{ $type->requires_attachment ? 'true' : 'false' }}"
                                data-max="{{ $type->max_days }}"
                                {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                            ({{ $balances[$type->id]->remaining_days ?? 0 }} days remaining)
                        </option>
                    @endforeach
                </select>
                @error('leave_type_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('start_date') border-red-400 @enderror"
                           required>
                    @error('start_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="endDate" value="{{ old('end_date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('end_date') border-red-400 @enderror"
                           required>
                    @error('end_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Working days preview --}}
            <div id="daysPreview" class="hidden p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-sm text-indigo-800">
                Estimated working days: <strong id="daysCount">—</strong>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('reason') border-red-400 @enderror"
                          placeholder="Please provide a reason for your leave request (minimum 10 characters)..." required>{{ old('reason') }}</textarea>
                @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Attachment --}}
            <div id="attachmentSection">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Attachment <span id="attachmentRequired" class="text-red-500 hidden">*</span>
                    <span class="text-gray-400 font-normal">(PDF, JPG, PNG — max 2MB)</span>
                </label>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('attachment') border border-red-400 rounded-lg @enderror">
                @error('attachment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">
                    Submit Request
                </button>
                <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function countWorkingDays(start, end) {
    let count = 0;
    let cur = new Date(start);
    let endDate = new Date(end);
    while (cur <= endDate) {
        const day = cur.getDay();
        if (day !== 0 && day !== 6) count++;
        cur.setDate(cur.getDate() + 1);
    }
    return count;
}

function updateDaysPreview() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    const preview = document.getElementById('daysPreview');
    const count = document.getElementById('daysCount');
    if (start && end && start <= end) {
        const days = countWorkingDays(start, end);
        count.textContent = days + ' working day(s)';
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}

document.getElementById('startDate').addEventListener('change', function () {
    document.getElementById('endDate').min = this.value;
    updateDaysPreview();
});
document.getElementById('endDate').addEventListener('change', updateDaysPreview);

document.getElementById('leaveTypeSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const req = opt.dataset.requiresAttachment === 'true';
    document.getElementById('attachmentRequired').classList.toggle('hidden', !req);
    const att = document.querySelector('[name="attachment"]');
    att.required = req;
});
</script>
@endsection
