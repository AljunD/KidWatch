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

            // Demo data (in real app this comes from Controller)
            $totalStudents = $role === 'teacher' ? 28 : 2;
            $childrenLabel = $role === 'teacher' ? 'Total Students' : 'My Children';
            $weeksTracked = 12;
            $avgRating = 3.4;
            $currentWeek = 12; // from Weeks table
            $currentWeekLabel = 'Apr 14 – Apr 20, 2026';
        @endphp

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div>
                <h1 class="text-5xl font-black tracking-[-2px] text-[#003366]">Dashboard</h1>
                <p class="mt-2 text-xl text-slate-600">Good {{ $greetingTime }}, <span class="font-semibold">{{ $displayName }}</span> 👋</p>
                <p class="text-slate-500">Here's what's happening with your kids this week</p>
            </div>

            <div class="flex items-center gap-x-4 bg-white rounded-3xl px-6 py-4 shadow-sm border border-slate-100">
                <div class="text-right">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#0077cc]">Week {{ $currentWeek }}</p>
                    <p class="text-lg font-semibold text-[#003366]">{{ $currentWeekLabel }}</p>
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
                        <p class="text-sm font-semibold text-slate-500">{{ $childrenLabel }}</p>
                        <p class="text-5xl font-black text-[#003366] mt-2">{{ $totalStudents }}</p>
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
                    <span>+2 this month</span>
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
                    <span class="font-mono">12/52</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full w-[23%] bg-[#003366]"></div>
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
                <p class="text-xs text-emerald-600 mt-6 font-medium">↑ 0.3 from last week</p>
            </div>

            {{-- Card 4: Smart Recommendations --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">Recommendations</p>
                        <p class="text-5xl font-black text-[#003366] mt-2">7</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-3xl flex items-center justify-center text-3xl">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="absolute bottom-6 right-6 text-[10px] font-black uppercase bg-purple-100 text-purple-700 px-4 h-7 rounded-3xl flex items-center">
                    Ready to apply
                </div>
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
                            {{-- Demo rows - in real app loop through progress_records with eager loading --}}
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-5 font-medium">Emma Thompson</td>
                                <td class="py-5">Language</td>
                                <td class="py-5 text-slate-500">Week 12</td>
                                <td class="py-5 text-center">
                                    <div class="inline-flex items-center justify-center px-4 h-8 bg-emerald-100 text-emerald-700 rounded-3xl text-sm font-semibold">Excellent (4)</div>
                                </td>
                                <td class="py-5 text-right">
                                    <button onclick="viewProgressDetail(1)"
                                            class="text-[#0077cc] hover:text-[#003366] text-xs font-semibold">Details →</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-5 font-medium">Liam Santos</td>
                                <td class="py-5">Math</td>
                                <td class="py-5 text-slate-500">Week 12</td>
                                <td class="py-5 text-center">
                                    <div class="inline-flex items-center justify-center px-4 h-8 bg-amber-100 text-amber-700 rounded-3xl text-sm font-semibold">Good (2)</div>
                                </td>
                                <td class="py-5 text-right">
                                    <button onclick="viewProgressDetail(2)"
                                            class="text-[#0077cc] hover:text-[#003366] text-xs font-semibold">Details →</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-5 font-medium">Mia Reyes</td>
                                <td class="py-5">Arts</td>
                                <td class="py-5 text-slate-500">Week 11</td>
                                <td class="py-5 text-center">
                                    <div class="inline-flex items-center justify-center px-4 h-8 bg-red-100 text-red-700 rounded-3xl text-sm font-semibold">Poor (1)</div>
                                </td>
                                <td class="py-5 text-right">
                                    <button onclick="viewProgressDetail(3)"
                                            class="text-[#0077cc] hover:text-[#003366] text-xs font-semibold">Details →</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-5 font-medium">Noah Cruz</td>
                                <td class="py-5">Science</td>
                                <td class="py-5 text-slate-500">Week 12</td>
                                <td class="py-5 text-center">
                                    <div class="inline-flex items-center justify-center px-4 h-8 bg-blue-100 text-blue-700 rounded-3xl text-sm font-semibold">Very Good (3)</div>
                                </td>
                                <td class="py-5 text-right">
                                    <button onclick="viewProgressDetail(4)"
                                            class="text-[#0077cc] hover:text-[#003366] text-xs font-semibold">Details →</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIGHT COLUMN: Weekly Summary + Recommendations --}}
            <div class="xl:col-span-5 space-y-8">

                {{-- Weekly Summary Card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 h-full">
                    <h2 class="text-2xl font-semibold text-[#003366] mb-4">This Week’s Summary</h2>
                    <div class="bg-slate-50 rounded-3xl p-5 text-slate-600 text-[15px] leading-relaxed">
                        Emma is showing excellent language skills and creativity. Liam needs extra support in Math — consider using blocks for visual learning. Mia continues to shine in Arts. Overall group energy is high!
                    </div>
                    <div class="flex justify-between items-center mt-6 text-xs">
                        <span class="font-medium text-slate-400">Generated from weekly_summaries table</span>
                        <a href="{{ route('progress') }}" class="text-[#0077cc] hover:underline font-semibold">Edit summary →</a>
                    </div>
                </div>

                {{-- Smart Recommendations (tied to recommendation_engine_configs) --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-2xl font-semibold text-[#003366]">Smart Interventions</h2>
                        <span class="px-4 py-1 text-xs font-black bg-purple-100 text-purple-700 rounded-3xl">Based on rating_level &lt; 2</span>
                    </div>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-red-100 text-red-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-xl">📐</div>
                            <div class="flex-1">
                                <p class="font-semibold">Liam – Math (Rating 1)</p>
                                <p class="text-sm text-slate-600 mt-px">Use physical blocks and counting games. Intervention: “Pair with a buddy for hands-on practice.”</p>
                                <a href="#" class="text-[#0077cc] text-xs mt-2 inline-flex items-center gap-1 hover:underline">Apply now <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 bg-red-100 text-red-600 rounded-2xl flex-shrink-0 flex items-center justify-center text-xl">🎨</div>
                            <div class="flex-1">
                                <p class="font-semibold">Mia – Arts (Rating 1)</p>
                                <p class="text-sm text-slate-600 mt-px">Encourage free drawing time. Intervention: “Provide larger paper and bright colors to spark confidence.”</p>
                                <a href="#" class="text-[#0077cc] text-xs mt-2 inline-flex items-center gap-1 hover:underline">Apply now <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performance by Subject Chart --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-[#003366]">Subject Performance (Last 4 Weeks)</h2>
                <select id="subject-filter" class="bg-white border border-slate-200 text-sm rounded-3xl px-5 py-2 focus:outline-none">
                    <option>All Subjects</option>
                    <option>Language</option>
                    <option>Math</option>
                    <option>Science</option>
                    <option>Arts</option>
                </select>
            </div>

            <div class="h-80">
                <canvas id="subjectChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart.js + Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Progress Chart
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('subjectChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Week 9', 'Week 10', 'Week 11', 'Week 12'],
                    datasets: [
                        {
                            label: 'Language',
                            data: [4, 3, 4, 4],
                            backgroundColor: '#0077cc',
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Math',
                            data: [2, 2, 1, 3],
                            backgroundColor: '#f59e0b',
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Science',
                            data: [3, 4, 3, 4],
                            backgroundColor: '#10b981',
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Arts',
                            data: [4, 3, 2, 1],
                            backgroundColor: '#8b5cf6',
                            borderRadius: 8,
                            borderSkipped: false,
                        }
                    ]
                },
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
                            ticks: { stepSize: 1, callback: (v) => ['Poor','Good','Very Good','Excellent'][v-1] }
                        },
                        x: { grid: { color: '#f1f5f9' } }
                    }
                }
            });

            // Demo: Clickable progress rows
            window.viewProgressDetail = function(id) {
                alert(`🔍 Opening detailed progress record #${id} (student_id + week_id + subject from progress_records table)`);
                // In real app: Livewire / Inertia / AJAX modal with full record + recommendation_engine_configs lookup
            };
        });
    </script>
</x-layout>
