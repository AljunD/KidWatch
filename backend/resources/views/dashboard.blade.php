{{-- resources/views/dashboard.blade.php --}}
<x-layout>
    <div class="space-y-10">
        {{-- Page Header + Greeting --}}
        @php
            $user = Auth::user();
            $role = $user->role;
            $profile = $role === 'teacher'
                ? ($user->teacher ?? (object)['first_name' => 'Teacher'])
                : ($user->guardian ?? (object)['first_name' => 'Parent']);
            $displayName = $profile->first_name . ' ' . ($profile->last_name ?? '');
            $greetingTime = now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening');
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <h1 class="text-5xl font-black tracking-[-2px] text-[#003366]">Dashboard</h1>
                <p class="mt-2 text-xl text-slate-600">
                    Good {{ $greetingTime }}, <span class="font-semibold">{{ $displayName }}</span> 👋
                </p>
                <p class="text-slate-500">Here's what's happening with your kids this week</p>
            </div>

            <div class="flex items-center gap-x-4 bg-white rounded-3xl px-6 py-4 shadow-sm border border-slate-100">
                <div class="text-right">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#0077cc]">
                        Week {{ $currentWeek ?: '—' }}
                    </p>
                    <p class="text-lg font-semibold text-[#003366]">
                        @if($currentWeekStart && $currentWeekEnd)
                            {{ $currentWeekStart }} – {{ $currentWeekEnd }}
                        @else
                            No week data yet
                        @endif
                    </p>
                </div>
                <div class="w-px h-12 bg-slate-200"></div>
                <div class="flex items-center justify-center w-12 h-12 bg-[#003366] text-white rounded-3xl text-3xl shadow-inner">
                    📅
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            {{-- Card 1: Students / Children --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            {{ $role === 'teacher' ? 'Total Students' : 'My Children' }}
                        </p>
                        <p class="text-5xl font-black text-[#003366] mt-2">
                            {{ $totalStudents > 0 ? $totalStudents : 'No students yet' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-[#0077cc] rounded-3xl flex items-center justify-center text-3xl">
                        @if($role === 'teacher')
                            <i class="fas fa-user-graduate"></i>
                        @else
                            <i class="fas fa-child-reaching"></i>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 text-emerald-600 text-sm font-medium mt-6">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>{{ $totalStudents }} active</span>
                </div>
            </div>

            {{-- Card 2: Weeks Tracked --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Weeks Tracked</p>
                        <p class="text-5xl font-black text-[#003366] mt-2">{{ $weeksTracked }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center text-3xl">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
                <div class="text-xs text-slate-400 mt-8 flex items-center gap-1">
                    <span class="font-mono">{{ $weeksTracked }}/52</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full w-[{{ round(($weeksTracked/52)*100) }}%] bg-[#003366]"></div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Average Rating --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Avg. Rating</p>
                        <p class="text-5xl font-black text-[#003366] mt-2">{{ number_format($avgRating, 1) }}</p>
                        <div class="flex text-amber-400 text-xl mt-1">
                            @for($i = 1; $i <= 4; $i++)
                                <i class="fas fa-star {{ $i <= floor($avgRating) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center text-3xl">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <p class="text-xs text-emerald-600 mt-6 font-medium">
                    @if($previousAvgRating !== null)
                        Compared to last week:
                        @php $diff = $avgRating - $previousAvgRating; @endphp
                        @if($diff > 0)
                            ↑ {{ number_format($diff, 1) }}
                        @elseif($diff < 0)
                            ↓ {{ number_format(abs($diff), 1) }}
                        @else
                            No change
                        @endif
                    @else
                        No previous data
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

            {{-- LEFT COLUMN: Recent Progress Log --}}
            <div class="xl:col-span-7 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-[#003366]">Recent Progress</h2>
                    <a href="{{ route('progress') }}"
                    class="text-sm font-semibold text-[#0077cc] hover:underline flex items-center gap-1">
                        View full log <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs font-medium text-slate-400">
                                <th class="text-left pb-4">Student</th>
                                <th class="text-left pb-4">Subject</th>
                                <th class="text-left pb-4">Week</th>
                                <th class="text-center pb-4">Rating</th>
                                <th class="w-28 pb-4"></th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y">
                            @forelse($progressRecords as $record)
                                @php
                                    // Map rating_level to label and color
                                    $labels = [
                                        0 => ['label' => 'No Classes', 'color' => 'gray'],
                                        1 => ['label' => 'Poor', 'color' => 'red'],
                                        2 => ['label' => 'Good', 'color' => 'amber'],
                                        3 => ['label' => 'Very Good', 'color' => 'blue'],
                                        4 => ['label' => 'Excellent', 'color' => 'emerald'],
                                    ];
                                    $rating = $labels[$record->rating_level] ?? ['label' => 'Unknown', 'color' => 'gray'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-5 font-medium">{{ $record->student->first_name }} {{ $record->student->last_name }}</td>
                                    <td class="py-5">{{ $record->subject }}</td>
                                    <td class="py-5 text-slate-500">Week {{ $record->week->number }}</td>
                                    <td class="py-5 text-center">
                                        <div class="inline-flex items-center justify-center px-4 h-8 
                                            bg-{{ $rating['color'] }}-100 text-{{ $rating['color'] }}-700 
                                            rounded-3xl text-sm font-semibold">
                                            {{ $rating['label'] }} ({{ $record->rating_level }})
                                        </div>
                                    </td>
                                    <td class="py-5 text-right">
                                        <button onclick="viewProgressDetail({{ $record->id }})"
                                                class="text-[#0077cc] hover:text-[#003366] text-xs font-semibold">
                                            Details →
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-400 italic">
                                        No progress records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            {{-- RIGHT COLUMN: Weekly Summary + Recommendations --}}
            <div class="xl:col-span-5 space-y-8">

                {{-- Weekly Summary Card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 h-full">
                    <h2 class="text-2xl font-semibold text-[#003366] mb-4">This Week’s Summary</h2>
                    <div class="bg-slate-50 rounded-3xl p-5 text-slate-600 text-[15px] leading-relaxed">
                        {{ $weeklySummary->content ?? 'No summary available yet.' }}
                    </div>
                    <div class="flex justify-between items-center mt-6 text-xs">
                        <span class="font-medium text-slate-400">Generated from weekly_summaries table</span>
                        <a href="{{ route('progress') }}" class="text-[#0077cc] hover:underline font-semibold">Edit summary →</a>
                    </div>
                </div>

            {{-- Performance by Subject Chart --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-[#003366]">Subject Performance (Last 4 Weeks)</h2>
                    <select id="subject-filter" class="bg-white border border-slate-200 text-sm rounded-3xl px-5 py-2 focus:outline-none">
                        <option value="all">All Subjects</option>
                        <option value="Language">Language</option>
                        <option value="Math">Math</option>
                        <option value="Science">Science</option>
                        <option value="Arts">Arts</option>
                    </select>
                </div>

                <div class="h-80">
                    <canvas id="subjectChart"></canvas>
                </div>
            </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const rawData = @json($chartData);

        const weeks = [...new Set(rawData.map(r => 'Week ' + r.week_id))];
        const subjects = [...new Set(rawData.map(r => r.subject))];

        const colors = {
            Language: '#0077cc',
            Math: '#f59e0b',
            Science: '#10b981',
            Arts: '#8b5cf6'
        };

        const datasets = subjects.map(sub => ({
            label: sub,
            data: weeks.map(week => {
                const rec = rawData.find(r => 'Week ' + r.week_id === week && r.subject === sub);
                return rec ? parseFloat(rec.avg_rating) : null;
            }),
            backgroundColor: colors[sub] || '#ccc',
            borderRadius: 8,
            borderSkipped: false,
        }));

        const ctx = document.getElementById('subjectChart');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: { labels: weeks, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, padding: 25, boxWidth: 8 } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        min: 1,
                        max: 4,
                        ticks: {
                            stepSize: 1,
                            callback: v => ['Poor','Good','Very Good','Excellent'][v-1]
                        }
                    },
                    x: { grid: { color: '#f1f5f9' } }
                }
            }
        });

        document.getElementById('subject-filter').addEventListener('change', e => {
            const selected = e.target.value;
            chart.data.datasets = selected === 'all'
                ? datasets
                : datasets.filter(d => d.label === selected);
            chart.update();
        });
    });
    </script>
</x-layout>
