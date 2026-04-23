<x-layout>
    <x-slot:title>Recommendations | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-10">
        <h1 class="text-3xl font-black text-[#003366] mb-6 border-b pb-4">
            💡 Weekly Recommendations
        </h1>

        @foreach($weeks as $week)
            <div class="rounded-2xl shadow-md p-6 space-y-6 bg-slate-50 border border-slate-200">
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
                                <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Student</th>
                                <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($students as $student)
                                @php
                                    // Check if student has ratings for all subjects in this week
                                    $hasAllRatings = true;
                                    foreach($subjects as $subject) {
                                        $record = $student->progressRecords
                                            ->where('week_id', $week->id)
                                            ->where('subject', $subject)
                                            ->first();

                                        if(!$record || $record->rating_level === null) {
                                            $hasAllRatings = false;
                                            break;
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-[#003366]">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($hasAllRatings)
                                            <span class="text-emerald-600 font-bold">✅ Complete</span>
                                        @else
                                            <span class="text-red-600 font-bold">❌ Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($hasAllRatings)
                                            <a href="{{ route('recommendation.detail', ['student' => $student->id, 'week' => $week->id]) }}"
                                               class="px-4 py-2 bg-purple-600 text-white rounded-lg font-bold shadow hover:bg-purple-700 transition">
                                                Generate Recommendation
                                            </a>
                                        @else
                                            <button disabled
                                                    class="px-4 py-2 bg-gray-200 text-gray-400 rounded-lg font-bold cursor-not-allowed">
                                                Generate Recommendation
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
