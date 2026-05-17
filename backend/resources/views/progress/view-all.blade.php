<x-layout>
    <x-slot:title>Student Progress | KidWatch</x-slot>

    <div class="max-w-6xl mx-auto bg-white rounded-3xl shadow-lg p-10 space-y-10">

        <div class="border rounded-xl p-6 bg-slate-50">
            <h1 class="text-3xl font-black text-[#003366]">Progress Report</h1>
            <p class="text-gray-600 mt-1">
                {{ $student->first_name }} {{ $student->last_name }}
            </p>
        </div>

        <div class="border rounded-xl p-4 bg-slate-50">
            <a href="{{ route('progress') }}"
               class="inline-flex items-center gap-2 bg-gray-200 text-[#003366] px-5 py-2 rounded-lg font-bold hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="space-y-3">
            @if(session('success'))
                <div class="px-6 py-4 rounded-xl bg-emerald-100 text-emerald-800 font-semibold shadow flex justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="px-6 py-4 rounded-xl bg-red-100 text-red-800 font-semibold shadow flex justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif
        </div>

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
                $summary = $summaries[$student->id . '-' . $week->id][0] ?? null;
            @endphp

            <div class="rounded-2xl p-6 space-y-6 border
                {{ $week->id === $latestWeekId ? 'bg-emerald-50 border-emerald-400' : 'bg-slate-50' }}">

                <div class="border rounded-xl p-4 bg-white flex justify-between items-center">
                    <h2 class="text-xl font-black text-[#003366]">Week {{ $week->week_number }}</h2>
                    @if($week->id === $latestWeekId)
                        <span class="text-xs bg-emerald-600 text-white px-3 py-1 rounded-full font-bold">Current</span>
                    @endif
                </div>

                <div class="border rounded-xl p-3 bg-slate-50">
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} –
                        {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($subjects as $subject)
                        @php
                            $record = $student->progressRecords
                                ->where('week_id', $week->id)
                                ->where('subject', $subject)
                                ->first();
                        @endphp

                        <div class="bg-white rounded-xl p-4 border shadow-sm">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-[#003366] capitalize">{{ $subject }}</h4>
                                @if($record)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $classMap[$record->rating_level] }}">
                                        {{ $ratings[$record->rating_level] ?? 'Lvl '.$record->rating_level }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Pending</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $record->remarks ?? 'No remarks provided.' }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="border rounded-xl p-4 bg-slate-50 flex justify-end">
                    @php
                        $allSubjectsRated = true;
                        foreach($subjects as $subject) {
                            $record = $student->progressRecords
                                ->where('week_id', $week->id)
                                ->where('subject', $subject)
                                ->first();
                            if(!$record || $record->rating_level === null) {
                                $allSubjectsRated = false;
                                break;
                            }
                        }
                    @endphp

                    @if($allSubjectsRated)
                        <form action="{{ route('progress.generateRecommendation', ['student' => $student->id, 'week' => $week->id]) }}" method="POST">
                            @csrf
                            <button class="px-6 py-2 {{ $summary ? 'bg-amber-600 hover:bg-amber-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white rounded-lg font-bold shadow">
                                {{ $summary ? 'Regenerate Summary' : 'Generate Summary' }}
                            </button>
                        </form>
                    @else
                        <button disabled class="px-6 py-2 bg-gray-200 text-gray-400 rounded-lg font-bold">Generate Summary</button>
                    @endif
                </div>

                @if($summary)
                    @php
                        $activities = json_decode($summary->activities_text, true);
                        if (!is_array($activities)) {
                            $activities = [];
                        }
                    @endphp

                    <div class="space-y-6">
                        <div class="bg-white border rounded-2xl p-6 shadow-sm">
                            <h3 class="font-black text-[#003366] mb-3">📘 Weekly Summary</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $summary->summary_text }}
                            </p>
                        </div>

                        <div class="bg-slate-100 p-4 rounded-xl">
                            <h4 class="font-bold text-[#003366] mb-2">Learning Highlights</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($subjects as $subject)
                                    @php
                                        $record = $student->progressRecords
                                            ->where('week_id', $week->id)
                                            ->where('subject', $subject)
                                            ->first();
                                        $level = $record->rating_level ?? null;
                                    @endphp
                                    @if($record)
                                        <span class="px-3 py-1 text-xs rounded-full font-bold
                                            @if($level >= 3) bg-emerald-100 text-emerald-700
                                            @elseif($level == 2) bg-amber-100 text-amber-700
                                            @elseif($level == 1) bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-500
                                            @endif">
                                            {{ ucfirst($subject) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-bold text-[#003366]">Suggested Activities & Tips</h4>
                            @foreach($activities as $category => $subjectsGroup)
                                @if(is_array($subjectsGroup))
                                    @foreach($subjectsGroup as $subject => $data)
                                        <div class="bg-white border rounded-xl p-5 shadow-sm">
                                            <h5 class="font-bold text-[#003366] mb-2">{{ ucfirst($subject) }}</h5>

                                            @if(!empty($data['recommendation']))
                                                <p class="text-gray-700 mb-2">
                                                    <strong>Activity:</strong> {{ $data['recommendation'] }}
                                                </p>
                                            @endif

                                            @if(!empty($data['narrative']))
                                                <p class="text-sm text-gray-600 mb-3">
                                                    {{ $data['narrative'] }}
                                                </p>
                                            @endif

                                            <div class="grid md:grid-cols-2 gap-3 text-sm">
                                                @if(!empty($data['guardian_tip']))
                                                    <div class="bg-blue-50 p-3 rounded-lg border">
                                                        <strong>Guardian Tip:</strong>
                                                        <p>{{ $data['guardian_tip'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($data['student_tip']))
                                                    <div class="bg-emerald-50 p-3 rounded-lg border">
                                                        <strong>Student Tip:</strong>
                                                        <p>{{ $data['student_tip'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>
</x-layout>
