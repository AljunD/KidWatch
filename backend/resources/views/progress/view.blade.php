<x-layout>
    <x-slot:title>Progress Details | KidWatch</x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-8">
        <h1 class="text-3xl font-black text-[#003366] mb-4 border-b pb-4">
            📊 Progress for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
            - Week {{ $week->week_number }}
        </h1>

        <p class="text-slate-500 font-medium mb-6">
            {{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} –
            {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }}
        </p>

        <div class="overflow-hidden border border-slate-200 rounded-xl shadow-sm">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Subject</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">Rating</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($subjects as $subject)
                        @php
                            $record = $student->progressRecords->where('subject', $subject)->first();
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-[#003366]">{{ $subject }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($record)
                                    <span class="inline-block px-4 py-1 rounded-full text-xs font-bold uppercase
                                        @switch($record->rating_level)
                                            @case(1) bg-red-100 text-red-700 @break
                                            @case(2) bg-amber-100 text-amber-700 @break
                                            @case(3) bg-blue-100 text-blue-700 @break
                                            @case(4) bg-emerald-100 text-emerald-700 @break
                                            @default bg-gray-100 text-gray-500
                                        @endswitch">
                                        {{ $ratings[$record->rating_level] ?? 'Lvl '.$record->rating_level }}
                                    </span>
                                @else
                                    <span class="text-slate-300 italic">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($record && $record->remarks)
                                    {{ $record->remarks }}
                                @else
                                    <span class="text-slate-300 italic">No remarks</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center mt-8">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</x-layout>
