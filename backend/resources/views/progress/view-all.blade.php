<x-layout>
    <x-slot:title>Student Progress | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-10">
        <h1 class="text-3xl font-black text-[#003366] mb-6 border-b pb-4">
            📊 Progress Records for
            <span class="text-emerald-600">
                {{ $student->first_name }} {{ $student->last_name }}
            </span>
        </h1>

        <div class="mb-6">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back to Progress Management
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-100 text-emerald-800 font-semibold shadow flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-100 text-red-800 font-semibold shadow flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 px-6 py-4 rounded-xl bg-yellow-100 text-yellow-800 font-semibold shadow">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $sortedWeeks = $weeks->sortByDesc('week_number');
            $latestWeekId = $sortedWeeks->first()->id ?? null;

            $classMap = [
                0 => 'bg-gray-100 text-gray-500',
                1 => 'bg-red-100 text-red-700',
                2 => 'bg-amber-100 text-amber-700',
                3 => 'bg-blue-100 text-blue-700',
                4 => 'bg-emerald-100 text-emerald-700',
            ];
        @endphp

        @foreach($sortedWeeks as $week)
            @php
                $allSubjectsRated = true;
                foreach($subjects as $subject) {
                    $record = $student->progressRecords
                        ->where('week_id', $week->id)
                        ->where('subject', $subject)
                        ->first();
                    if(!$record) {
                        $allSubjectsRated = false;
                        break;
                    }
                }

                $summary = $summaries[$student->id . '-' . $week->id][0] ?? null;
            @endphp

            <div class="rounded-2xl shadow-md p-6 space-y-6
                {{ $week->id === $latestWeekId ? 'bg-emerald-50 border-2 border-emerald-400' : 'bg-slate-50 border border-slate-200' }}"
                @if($week->id === $latestWeekId) id="current-week" @endif>

                <h2 class="text-xl font-black text-[#003366]">
                    Week {{ $week->week_number }}
                    <span class="text-slate-400 font-medium ml-2">
                        ({{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} –
                        {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }})
                    </span>
                    @if($week->id === $latestWeekId)
                        <span class="ml-3 inline-block px-3 py-1 text-xs font-bold bg-emerald-600 text-white rounded-full">
                            Current Week
                        </span>
                    @endif
                </h2>

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
                                    $ratingClass = $record ? ($classMap[$record->rating_level] ?? 'bg-gray-50 text-gray-400') : '';
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-semibold text-[#003366]">{{ $subject }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($record)
                                            <span class="inline-block px-4 py-1 rounded-full text-xs font-bold uppercase {{ $ratingClass }}">
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

                <div class="mt-4 flex justify-end">
                    @if($allSubjectsRated)
                        <form action="{{ route('progress.generateRecommendation', ['student' => $student->id, 'week' => $week->id]) }}" method="POST">
                            @csrf
                            <button class="px-6 py-2 {{ $summary ? 'bg-amber-600 hover:bg-amber-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white rounded-lg font-bold shadow transition">
                                {{ $summary ? '🔄 Regenerate Recommendation' : '📌 Generate Recommendation' }}
                            </button>
                        </form>
                    @else
                        <button disabled
                                class="px-6 py-2 bg-gray-200 text-gray-400 rounded-lg font-bold cursor-not-allowed">
                            📌 Generate Recommendation
                        </button>
                    @endif
                </div>

                @if($summary)
                    @php
                        $activities = json_decode($summary->activities_text, true);
                        if (!is_array($activities)) {
                            $activities = [];
                        }
                    @endphp

                    <div class="mt-4 p-4 bg-emerald-50 border border-emerald-300 rounded-lg">
                        <h4 class="font-bold text-[#003366] mb-2">Recommendation Summary</h4>
                        <p class="text-gray-700 whitespace-pre-line">{{ $summary->summary_text }}</p>

                        <h4 class="font-bold text-[#003366] mt-4 mb-2">Recommendation Activities</h4>
                        <ul class="list-disc pl-6 text-gray-700 space-y-4">
                            @foreach($activities as $activity)
                                @if(is_array($activity))
                                    <li>
                                        <p class="font-medium">{{ $activity['activity'] ?? '' }}</p>

                                        <span class="text-xs px-2 py-1 rounded
                                            @if(($activity['priority'] ?? '') === 'high') bg-red-100 text-red-700
                                            @elseif(($activity['priority'] ?? '') === 'medium') bg-yellow-100 text-yellow-700
                                            @else bg-green-100 text-green-700
                                            @endif">
                                            Priority: {{ ucfirst($activity['priority'] ?? 'low') }}
                                        </span>

                                        @if(!empty($activity['guardian_tip']))
                                            <p class="mt-2 text-sm text-blue-800">
                                                <strong>Guardian Tip:</strong> {{ $activity['guardian_tip'] }}
                                            </p>
                                        @endif

                                        @if(!empty($activity['student_tip']))
                                            <p class="mt-1 text-sm text-green-800">
                                                <strong>Student Tip:</strong> {{ $activity['student_tip'] }}
                                            </p>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-layout>
