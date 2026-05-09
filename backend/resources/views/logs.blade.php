<x-layout>
    <x-slot:title>KidWatch | System Logs</x-slot>

    <div class="max-w-6xl mx-auto mb-6">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
    </div>
            <div>
                <h1 class="text-4xl font-black text-[#003366] tracking-[-1px] leading-none uppercase italic">System Logs</h1>
                <p class="text-slate-500">Here's what's happening in your system</p>
            </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        @forelse($logs as $log)
            <div class="bg-gray-50 rounded-lg p-4 mb-4 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ ucfirst($log->action) }} — {{ $log->entity_type }} #{{ $log->entity_id }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $log->action_label }} — {{ $log->entity_type }} #{{ $log->entity_id }} <br>
                            By: {{ $log->user->name ?? 'System' }} at {{ $log->formatted_date }}
                        </p>
                    </div>
                </div>

                @if($log->details)
                    <details class="mt-3">
                        <summary class="cursor-pointer text-blue-600 font-medium flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Details
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-6">
                            {{ $log->details }}
                        </p>
                    </details>
                @endif
            </div>
        @empty
            <p class="text-gray-400 italic">No logs available</p>
        @endforelse

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </div>
</x-layout>
