<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Approval Letter — {{ $leaveRequest->user->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .letter-card { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

{{-- Toolbar (hidden on print) --}}
<div class="no-print bg-indigo-900 text-white px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('leave-requests.show', $leaveRequest) }}"
           class="flex items-center gap-2 text-indigo-300 hover:text-white text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Request
        </a>
    </div>
    <button onclick="window.print()"
            class="flex items-center gap-2 bg-white text-indigo-900 text-sm font-semibold px-4 py-1.5 rounded-lg hover:bg-indigo-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print Letter
    </button>
</div>

{{-- Letter --}}
<div class="max-w-2xl mx-auto my-8 px-4">
    <div class="letter-card bg-white shadow-lg rounded-xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-indigo-900 px-10 py-8 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-wide">{{ config('app.name') }}</h1>
                    <p class="text-indigo-300 text-sm">Human Resources Department</p>
                </div>
            </div>
        </div>

        {{-- Letter Body --}}
        <div class="px-10 py-8 space-y-6 text-gray-800 text-sm leading-relaxed">

            {{-- Reference & Date --}}
            <div class="flex justify-between text-xs text-gray-500">
                <span>Ref: LR-{{ str_pad($leaveRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span>{{ $leaveRequest->approved_at?->format('d F Y') }}</span>
            </div>

            {{-- Subject --}}
            <div>
                <p class="font-semibold text-base text-gray-900">LEAVE APPROVAL LETTER</p>
                <div class="mt-1 w-16 h-0.5 bg-indigo-600 rounded"></div>
            </div>

            {{-- Addressee --}}
            <div class="space-y-0.5">
                <p class="font-semibold text-gray-900">{{ $leaveRequest->user->name }}</p>
                <p class="text-gray-500">{{ $leaveRequest->user->department }}</p>
                <p class="text-gray-500">{{ config('app.name') }}</p>
            </div>

            {{-- Salutation --}}
            <p>Dear <strong>{{ $leaveRequest->user->name }}</strong>,</p>

            {{-- Body --}}
            <p>
                This is to confirm that your application for
                <strong>{{ $leaveRequest->leaveType->name }}</strong>
                has been reviewed and <strong class="text-green-700">approved</strong>.
            </p>

            {{-- Leave Details Table --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-gray-50">
                            <td class="px-4 py-2.5 font-medium text-gray-600 w-40">Leave Type</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $leaveRequest->leaveType->name }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-600">Start Date</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $leaveRequest->start_date->format('D, d F Y') }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-4 py-2.5 font-medium text-gray-600">End Date</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $leaveRequest->end_date->format('D, d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-600">Working Days</td>
                            <td class="px-4 py-2.5 font-semibold text-gray-900">{{ $leaveRequest->total_days }} day(s)</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-4 py-2.5 font-medium text-gray-600">Leave Category</td>
                            <td class="px-4 py-2.5 text-gray-900">{{ $leaveRequest->leaveType->paid ? 'Paid Leave' : 'Unpaid Leave' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($leaveRequest->manager_comment)
            <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                <p class="text-xs font-medium text-indigo-700 uppercase tracking-wider mb-1">HR Comment</p>
                <p class="text-gray-700 italic">{{ $leaveRequest->manager_comment }}</p>
            </div>
            @endif

            <p>
                Please ensure all pending responsibilities are handed over before your leave commences.
                This letter serves as your official leave authorisation.
            </p>

            <p>Wishing you a pleasant leave.</p>

            {{-- Signature --}}
            <div class="pt-4">
                <p>Yours sincerely,</p>
                <div class="mt-6 space-y-0.5">
                    <p class="font-semibold text-gray-900">{{ $leaveRequest->approver->name }}</p>
                    <p class="text-gray-500">Human Resources</p>
                    <p class="text-gray-500">{{ config('app.name') }}</p>
                    <p class="text-gray-400 text-xs">{{ $leaveRequest->approved_at?->format('d F Y, H:i') }}</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="pt-4 border-t border-gray-100 text-center text-xs text-gray-400">
                This is a system-generated document — {{ config('app.name') }} Leave Management System
            </div>
        </div>

    </div>
</div>

</body>
</html>
