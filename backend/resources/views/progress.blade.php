{{-- resources/views/progress.blade.php --}}
<x-layout>
    <x-slot:title>Progress Management | KidWatch</x-slot>

    <div class="space-y-8">
        {{-- Header & Controls --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-[#003366] tracking-[-1px] leading-none uppercase italic">Student Progress</h1>
                <p class="text-slate-500 mt-2 font-medium">Track weekly academic performance • Real-time updates</p>
            </div>

            {{-- Week Creation Form --}}
            <form action="{{ route('weeks.store') }}" method="POST"
                  class="flex flex-wrap items-center gap-6 bg-white rounded-3xl px-6 py-4 shadow-sm border border-blue-100">
                @csrf
                <div class="flex items-center gap-3">
                    <label for="week_number" class="text-[10px] font-black uppercase tracking-widest text-[#003366]/70">Week #</label>
                    <input type="number" name="week_number" id="week_number"
                           class="bg-slate-50 border border-blue-100 rounded-2xl px-4 py-2 text-sm font-bold text-[#003366] focus:ring-2 focus:ring-blue-500 outline-none w-20" required>
                </div>
                <div class="flex items-center gap-3">
                    <label for="start_date" class="text-[10px] font-black uppercase tracking-widest text-[#003366]/70">Start</label>
                    <input type="date" name="start_date" id="start_date"
                           class="bg-slate-50 border border-blue-100 rounded-2xl px-4 py-2 text-sm font-bold text-[#003366] focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="flex items-center gap-3">
                    <label for="end_date" class="text-[10px] font-black uppercase tracking-widest text-[#003366]/70">End</label>
                    <input type="date" name="end_date" id="end_date"
                           class="bg-slate-50 border border-blue-100 rounded-2xl px-4 py-2 text-sm font-bold text-[#003366] focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <button type="submit"
                        class="bg-[#003366] text-white px-7 py-3 rounded-3xl font-black text-xs uppercase shadow-xl shadow-blue-900/20 hover:shadow-2xl active:scale-95 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Create New Week
                </button>
            </form>
        </div>

        {{-- Student List Section --}}
        <div class="bg-white rounded-3xl shadow-lg p-8 space-y-6">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6 border-b pb-3">
                <h2 class="text-2xl font-black text-[#003366] flex items-center gap-2">
                    👩‍🎓 Student List
                </h2>
                <span class="text-sm text-slate-500 font-medium">
                    Total: {{ $students->count() }} students
                </span>
            </div>

            {{-- Student Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($students as $student)
                    <div class="flex items-center gap-4 bg-gradient-to-r from-slate-50 to-white border border-slate-200 rounded-xl p-5 hover:shadow-lg hover:border-blue-200 transition-all group">

                        {{-- Avatar Circle --}}
                        <div class="w-12 h-12 rounded-full bg-[#003366] text-white flex items-center justify-center font-black text-lg shadow-inner group-hover:scale-105 transition-transform">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>

                        {{-- Student Info --}}
                        <div>
                            <p class="font-bold text-[#003366] text-lg group-hover:text-blue-700 transition-colors">
                                {{ $student->first_name }} {{ $student->last_name }}
                            </p>
                            <p class="text-slate-500 text-sm italic">
                                ID: {{ $student->id }}
                            </p>
                        </div>

                        {{-- Action Button --}}
                        <div class="ml-auto">
                            <a href="{{ route('progress.viewAll', ['student_id' => $student->id]) }}"
                            class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-blue-700 hover:shadow-md transition">
                                <i class="fas fa-chart-line"></i> View Progress
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Weeks - Current week always on top --}}
        <div class="space-y-6">
            @php
                $sortedWeeks = $weeks->sortByDesc('week_number');
            @endphp

            @foreach($sortedWeeks as $week)
                @php
                    $isLatest = $week->week_number === $weeks->max('week_number');
                @endphp

                <details class="group bg-white rounded-3xl border border-blue-100 shadow-sm open:shadow-2xl open:ring-2 open:ring-blue-400/20 transition-all">
                    <summary class="flex justify-between items-center px-8 py-7 bg-gradient-to-r from-slate-50 to-white hover:from-[#e3f2fd] cursor-pointer list-none">
                        <div class="flex items-center gap-5">
                            @if($isLatest)
                                <div class="inline-flex items-center gap-2 bg-[#003366] text-white text-xs font-black uppercase px-5 py-2 rounded-3xl shadow-md">
                                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                    CURRENT WEEK
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 text-xs font-black uppercase px-5 py-2 rounded-3xl">
                                    <i class="fas fa-check-circle"></i> COMPLETED
                                </div>
                            @endif

                            <h2 class="text-2xl font-black text-[#003366] tracking-tight">
                                Week {{ $week->week_number }}
                                <span class="text-slate-300 mx-3">•</span>
                                <span class="text-slate-500 text-base font-medium">
                                    {{ \Carbon\Carbon::parse($week->start_date)->format('M d') }} -
                                    {{ \Carbon\Carbon::parse($week->end_date)->format('M d, Y') }}
                                </span>
                            </h2>
                        </div>

                        <div class="flex items-center gap-4 text-xs font-black text-slate-400 group-open:text-[#003366]">
                            <span class="group-open:hidden">EXPAND ▼</span>
                            <span class="hidden group-open:block">COLLAPSE ▲</span>
                            <i class="fas fa-chevron-down transition-transform group-open:rotate-180"></i>
                        </div>
                    </summary>

                    <div class="border-t border-blue-100">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-8 py-6 text-left text-xs font-black uppercase tracking-widest text-[#003366]/70">Student Name</th>
                                        @foreach($subjects as $subject)
                                            <th class="px-6 py-6 text-center text-xs font-black uppercase tracking-widest text-[#003366]/70">{{ $subject }}</th>
                                        @endforeach
                                        <th class="px-8 py-6 text-right text-xs font-black uppercase tracking-widest text-[#003366]/70">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-blue-50">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-blue-50/60 group"
                                            data-student-id="{{ $student->id }}"
                                            data-week-id="{{ $week->id }}"
                                            data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-2xl bg-[#003366] text-white flex items-center justify-center font-black text-sm shadow-inner">
                                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                    </div>
                                                    <span class="font-bold text-[#003366]">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                </div>
                                            </td>

                                            @foreach($subjects as $subject)
                                                @php
                                                    $record = $student->progressRecords
                                                        ->where('week_id', $week->id)
                                                        ->where('subject', $subject)
                                                        ->first();
                                                @endphp
                                                <td class="px-6 py-6 text-center" data-subject="{{ $subject }}">
                                                    @if($record)
                                                        <span class="inline-block px-5 py-2 rounded-3xl font-black text-xs uppercase shadow-sm
                                                            @if($record->rating_level == 0) bg-gray-200 text-gray-600
                                                            @elseif($record->rating_level == 1) bg-red-100 text-red-700
                                                            @elseif($record->rating_level == 2) bg-amber-100 text-amber-700
                                                            @elseif($record->rating_level == 3) bg-blue-100 text-blue-700
                                                            @elseif($record->rating_level == 4) bg-emerald-100 text-emerald-700
                                                            @endif">
                                                            {{ $ratings[$record->rating_level] ?? 'Lvl '.$record->rating_level }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-300 font-medium text-sm italic">Pending</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            {{-- NEW ACTIONS COLUMN - View / Add / Edit --}}
                                            <td class="px-8 py-6 text-right">
                                                <div class="flex justify-end gap-2">

                                                    {{-- View Progress --}}
                                                    <a href="{{ route('progress.view', ['student_id' => $student->id, 'week_id' => $week->id]) }}"
                                                    class="w-9 h-9 bg-white border border-slate-200 hover:bg-blue-600 hover:text-white rounded-2xl flex items-center justify-center transition-all text-slate-600"
                                                    title="View Progress">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </a>

                                                    {{-- Add Progress --}}
                                                    @php
                                                        $gradedSubjectsCount = $student->progressRecords
                                                            ->where('week_id', $week->id)
                                                            ->count();

                                                        $allSubjectsGraded = $gradedSubjectsCount >= count($subjects);
                                                    @endphp

                                                    @if($allSubjectsGraded)
                                                        <button disabled
                                                            class="w-9 h-9 bg-gray-200 text-gray-400 rounded-2xl flex items-center justify-center cursor-not-allowed"
                                                            title="All subjects already graded">
                                                            <i class="fas fa-plus text-sm"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('progress.create', ['student_id' => $student->id, 'week_id' => $week->id]) }}"
                                                        class="w-9 h-9 bg-white border border-slate-200 hover:bg-emerald-600 hover:text-white rounded-2xl flex items-center justify-center transition-all text-slate-600"
                                                        title="Add Progress">
                                                            <i class="fas fa-plus text-sm"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Edit Progress --}}
                                                    @php
                                                        $firstRecord = $student->progressRecords
                                                            ->where('week_id', $week->id)
                                                            ->first();
                                                    @endphp

                                                    @if($firstRecord)
                                                        <a href="{{ route('progress.edit', $firstRecord->id) }}"
                                                        class="w-9 h-9 bg-white border border-slate-200 hover:bg-amber-600 hover:text-white rounded-2xl flex items-center justify-center transition-all text-slate-600"
                                                        title="Edit Progress">
                                                            <i class="fas fa-edit text-sm"></i>
                                                        </a>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Weekly Summary --}}
                        <div class="bg-slate-50 px-8 py-6 border-t">
                            <h3 class="text-xs font-black uppercase tracking-widest text-[#003366] flex items-center gap-2 mb-3">
                                <i class="fas fa-comment-dots"></i> Teacher Summary
                            </h3>
                            <p class="text-slate-600 italic text-sm">
                                "{{ $week->weeklySummaries->first()->summary_text ?? 'No summary recorded yet for this week.' }}"
                            </p>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('%c✅ Progress page ready - View / Add / Edit / Delete actions active', 'color:#003366; font-weight:bold');

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.progress-action-btn');
            if (!btn) return;

            const row = btn.closest('tr');
            if (!row) return;

            const action = btn.dataset.action;
            const studentId = row.dataset.studentId;
            const weekId = row.dataset.weekId;
            const studentName = row.dataset.studentName;

            if (!studentId || !weekId) return;

            switch (action) {
                case 'view':
                    // Redirect to summary route
                    window.location.href = `/progress/summary/${studentId}/${weekId}`;
                    break;

                case 'add':
                    // Redirect to create form with query params
                    window.location.href = `/progress/create?student_id=${studentId}&week_id=${weekId}`;
                    break;

                case 'edit':
                    // Redirect to edit form for this record
                    // You need the actual record ID here, not just student/week.
                    // If you only have student/week, you may need to fetch the record ID server-side.
                    const recordId = row.querySelector('[data-subject]')?.dataset.recordId;
                    if (recordId) {
                        window.location.href = `/progress/${recordId}/edit?week_id=${weekId}`;
                    } else {
                        alert(`No progress record found for ${studentName} (Week ${weekId})`);
                    }
                    break;

                case 'delete':
                    if (confirm(`🗑️ Delete ALL progress records for ${studentName} in Week ${weekId}?`)) {
                        // Redirect to destroy route (or send AJAX)
                        const recordId = row.querySelector('[data-subject]')?.dataset.recordId;
                        if (recordId) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/progress/${recordId}`;
                            form.innerHTML = `
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }
                    break;
            }
        });
    });
    </script>

</x-layout>
