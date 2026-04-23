<x-layout>
    <x-slot:title>Student Progress | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-10">
        <h1 class="text-3xl font-black text-[#003366] mb-6 border-b pb-4">
            📊 Progress Records for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
        </h1>

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>

        @php
            $today = \Carbon\Carbon::today();
            $currentWeek = $weeks->first(fn($w) => $today->between($w->start_date, $w->end_date));
            $sortedWeeks = $currentWeek
                ? collect([$currentWeek])->merge($weeks->where('id', '!=', $currentWeek->id)->sortByDesc('week_number'))
                : $weeks->sortByDesc('week_number');
        @endphp

        {{-- Loop through reordered weeks --}}
        @foreach($sortedWeeks as $week)
            @php
                $allSubjectsRated = true;
                foreach($subjects as $subject) {
                    $record = $student->progressRecords
                        ->where('week_id', $week->id)
                        ->where('subject', $subject)
                        ->first();

                    // Fail if no record exists at all
                    if(!$record) {
                        $allSubjectsRated = false;
                        break;
                    }
                }
            @endphp

            <div class="rounded-2xl shadow-md p-6 space-y-6
                @if($today->between($week->start_date, $week->end_date))
                    bg-emerald-50 border-2 border-emerald-400
                @else
                    bg-slate-50 border border-slate-200
                @endif"
                @if($today->between($week->start_date, $week->end_date)) id="current-week" @endif>

                <h2 class="text-xl font-black text-[#003366]">
                    Week {{ $week->week_number }}
                    <span class="text-slate-400 font-medium ml-2">
                        ({{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} –
                        {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }})
                    </span>
                </h2>

                {{-- Progress Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full border border-slate-200 rounded-lg">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Subject</th>
                                <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($subjects as $subject)
                                @php
                                    $record = $student->progressRecords
                                        ->where('week_id', $week->id)
                                        ->where('subject', $subject)
                                        ->first();
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-[#003366]">{{ $subject }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($record)
                                            <span class="inline-block px-4 py-1 rounded-full text-xs font-bold uppercase
                                                @switch($record->rating_level)
                                                    @case(0) bg-gray-100 text-gray-500 @break
                                                    @case(1) bg-red-100 text-red-700 @break
                                                    @case(2) bg-amber-100 text-amber-700 @break
                                                    @case(3) bg-blue-100 text-blue-700 @break
                                                    @case(4) bg-emerald-100 text-emerald-700 @break
                                                @endswitch">
                                                {{ $ratings[$record->rating_level] ?? 'Lvl '.$record->rating_level }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 italic">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $record->remarks ?? 'No remarks' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Generate Recommendation Button --}}
                <div class="mt-4 flex justify-end">
                    @if($allSubjectsRated)
                        <a href="{{ route('recommendation', ['student' => $student->id, 'week' => $week->id]) }}"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg font-bold shadow hover:bg-purple-700 transition">
                            📌 Generate Recommendation
                        </a>
                    @else
                        <button disabled
                                class="px-6 py-2 bg-gray-200 text-gray-400 rounded-lg font-bold cursor-not-allowed">
                            📌 Generate Recommendation
                        </button>
                    @endif
                </div
            </div>
        @endforeach
    </div>
</x-layout>
