<x-layout>
    <x-slot:title>Student Progress | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-10">
        <h1 class="text-3xl font-black text-[#003366] mb-6 border-b pb-4">
            📊 Progress Records for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
        </h1>

        {{-- Loop through all weeks --}}
        @foreach($weeks as $week)
            <div class="bg-slate-50 rounded-2xl shadow-md p-6 space-y-6">
                <h2 class="text-xl font-black text-[#003366]">
                    Week {{ $week->week_number }}
                    <span class="text-slate-400 font-medium ml-2">
                        ({{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} –
                         {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }})
                    </span>
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full border border-slate-200 rounded-lg">
                        <thead>
                            <tr class="bg-slate-100">
                                @foreach($subjects as $subject)
                                    <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">{{ $subject }}</th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-slate-50 transition">
                                {{-- Ratings per subject --}}
                                @foreach($subjects as $subject)
                                    @php
                                        $record = $student->progressRecords
                                            ->where('week_id', $week->id)
                                            ->where('subject', $subject)
                                            ->first();
                                    @endphp
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
                                @endforeach

                                {{-- Remarks --}}
                                <td class="px-6 py-4 text-slate-600">
                                    @php
                                        $remarks = $student->progressRecords
                                            ->where('week_id', $week->id)
                                            ->pluck('remarks')
                                            ->filter()
                                            ->implode('; ');
                                    @endphp
                                    {{ $remarks ?: 'No remarks' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Weekly Summary --}}
                <div class="bg-white border border-slate-200 rounded-xl p-4 mt-4">
                    <h3 class="text-xs font-black uppercase tracking-widest text-[#003366] mb-2 flex items-center gap-2">
                        <i class="fas fa-comment-dots"></i> Teacher Summary
                    </h3>
                    <p class="text-slate-600 italic text-sm">
                        "{{ $week->weeklySummaries->first()->summary_text ?? 'No summary recorded yet for this week.' }}"
                    </p>
                </div>
            </div>
        @endforeach

        {{-- Back Button --}}
        <div class="flex justify-between items-center mt-8">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>
    </div>
</x-layout>
