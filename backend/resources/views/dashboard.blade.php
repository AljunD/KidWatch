{{-- Updated resources/views/dashboard.blade.php (responsiveness matched to new sidenav) --}}
<x-layout>
    <x-slot:title>KidWatch | Admin Dashboard</x-slot>

    {{-- Top Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-4xl lg:text-5xl font-black text-[#003366] tracking-tighter leading-none">
                Good morning, {{ Auth::user()->teacher->first_name ?? 'Admin' }}! 👋
            </h1>
            <p class="text-slate-500 mt-3 text-lg font-medium">
                Here's what's happening at <span class="font-semibold text-[#003366]">Barangay Balite Day Care</span> today
            </p>
        </div>

        <div class="flex items-center gap-x-8 text-sm">
            <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-3xl shadow-sm border border-slate-100">
                <i class="fas fa-calendar text-[#003366]"></i>
                <span id="currentDate" class="font-semibold text-[#003366]"></span>
            </div>

            <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-3xl shadow-sm border border-slate-100">
                <i class="fas fa-clock text-[#003366]"></i>
                <span id="currentTime" class="font-semibold text-[#003366] tabular-nums"></span>
            </div>

            <span class="px-6 py-3 rounded-3xl bg-[#003366] text-white text-sm font-black uppercase tracking-widest shadow-inner flex items-center gap-2">
                <i class="fas fa-shield-halved"></i>
                {{ Auth::user()->role }} ACCOUNT
            </span>
        </div>
    </div>

    {{-- KPI Stats Grid (unchanged - already responsive) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5 border border-white hover:border-blue-200 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="uppercase text-xs font-black tracking-[1px] text-blue-600">Total Enrolled</span>
                    <div id="studentsCount" class="text-6xl font-black text-[#003366] mt-2 tracking-tighter">{{ $studentsCount ?? 0 }}</div>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-3xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">
                    👦
                </div>
            </div>
            <div class="mt-6 text-emerald-500 flex items-center gap-1 text-sm font-semibold">
                <i class="fas fa-arrow-trend-up"></i>
                <span>+3 this week</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5 border border-white hover:border-blue-200 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="uppercase text-xs font-black tracking-[1px] text-blue-600">Active Teachers</span>
                    <div id="teachersCount" class="text-6xl font-black text-[#003366] mt-2 tracking-tighter">{{ $teachersCount ?? 0 }}</div>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-3xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">
                    👩‍🏫
                </div>
            </div>
            <div class="mt-6 text-emerald-500 flex items-center gap-1 text-sm font-semibold">
                <i class="fas fa-arrow-trend-up"></i>
                <span>All online today</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5 border border-white hover:border-blue-200 transition-all group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="uppercase text-xs font-black tracking-[1px] text-blue-600">Present Today</span>
                    <div class="text-6xl font-black text-emerald-600 mt-2 tracking-tighter">{{ $presentToday ?? 37 }}</div>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-3xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">
                    ✅
                </div>
            </div>
            <div class="mt-6 flex items-center text-xs font-medium text-slate-400">
                <span class="flex-1 h-2 bg-emerald-200 rounded-full relative">
                    <span class="absolute left-0 top-0 h-2 bg-emerald-500 rounded-full" style="width: 92%"></span>
                </span>
                <span class="ml-3">92% of total</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-[#003366] to-blue-700 text-white rounded-3xl p-8 shadow-2xl shadow-blue-900/30 flex flex-col justify-between">
            <div class="flex justify-between">
                <div>
                    <span class="uppercase text-xs font-black tracking-[1px] opacity-75">Avg. Attendance</span>
                    <div class="text-6xl font-black mt-1">{{ $attendanceRate ?? '94' }}<span class="text-3xl">%</span></div>
                </div>
                <i class="fas fa-chart-line text-5xl opacity-30"></i>
            </div>
            <div class="text-blue-200 text-sm font-medium mt-auto pt-8 border-t border-white/20 flex justify-between items-center">
                <span>This month</span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-caret-up"></i> +4%
                </span>
            </div>
        </div>
    </div>

    {{-- Rest of dashboard content (unchanged - already perfectly responsive) --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <div class="xl:col-span-8 bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-[#003366]">Today's Attendance • {{ date('l, M d') }}</h2>
                <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-2">
                    Full report
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-slate-50 rounded-3xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex justify-between mb-4">
                        <div class="font-semibold">🐣 Starfish Room (Ages 3-4)</div>
                        <span class="text-xs font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-3xl">12 / 14</span>
                    </div>
                    <div class="flex -space-x-3">
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🧸</div>
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🦋</div>
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🐢</div>
                    </div>
                    <div class="text-xs text-slate-400 mt-4">2 absent • Checked in at 7:42 AM</div>
                </div>

                <div class="bg-slate-50 rounded-3xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex justify-between mb-4">
                        <div class="font-semibold">🌈 Rainbow Room (Ages 4-5)</div>
                        <span class="text-xs font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-3xl">18 / 19</span>
                    </div>
                    <div class="flex -space-x-3">
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🚀</div>
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🌟</div>
                    </div>
                    <div class="text-xs text-slate-400 mt-4">1 absent • All checked in</div>
                </div>

                <div class="bg-slate-50 rounded-3xl p-6 hover:bg-blue-50 transition-colors">
                    <div class="flex justify-between mb-4">
                        <div class="font-semibold">🐳 Ocean Room (Ages 2-3)</div>
                        <span class="text-xs font-black bg-amber-100 text-amber-700 px-3 py-1 rounded-3xl">9 / 11</span>
                    </div>
                    <div class="flex -space-x-3">
                        <div class="w-8 h-8 bg-white border-2 border-slate-50 rounded-3xl flex items-center justify-center text-lg shadow-sm">🐠</div>
                    </div>
                    <div class="text-xs text-slate-400 mt-4">2 late • Last check-in 8:11 AM</div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-4 flex flex-col gap-8">
            <div class="flex-1 bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5">
                <h3 class="font-bold text-[#003366] mb-6 flex items-center gap-2">
                    <i class="fas fa-bolt"></i> Live Activity
                </h3>
                <div class="space-y-6 text-sm">
                    <div class="flex gap-4">
                        <div class="w-2 h-2 mt-1.5 bg-emerald-400 rounded-full animate-pulse"></div>
                        <div class="flex-1">
                            <div><span class="font-semibold">Maria Santos</span> checked in at 7:38 AM</div>
                            <div class="text-xs text-slate-400">Starfish Room • Guardian: Ana Santos</div>
                        </div>
                        <span class="text-[10px] text-emerald-500 font-medium">just now</span>
                    </div>
                    <!-- more activity items unchanged -->
                </div>
            </div>

            <div class="bg-gradient-to-br from-[#003366] to-blue-700 text-white rounded-3xl p-8 shadow-2xl flex flex-col">
                <h3 class="font-bold mb-6">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <button onclick="alert('📍 Check-in flow would open here')"
                            class="bg-white/10 hover:bg-white/20 transition-colors py-6 rounded-3xl flex flex-col items-center gap-2">
                        <i class="fas fa-door-open text-2xl"></i>
                        <span class="font-semibold text-sm">Mark Check-in</span>
                    </button>
                    <button onclick="alert('📤 Check-out flow would open here')"
                            class="bg-white/10 hover:bg-white/20 transition-colors py-6 rounded-3xl flex flex-col items-center gap-2">
                        <i class="fas fa-door-closed text-2xl"></i>
                        <span class="font-semibold text-sm">Mark Check-out</span>
                    </button>
                    <button onclick="alert('📸 Photo upload would open here')"
                            class="bg-white/10 hover:bg-white/20 transition-colors py-6 rounded-3xl flex flex-col items-center gap-2">
                        <i class="fas fa-camera text-2xl"></i>
                        <span class="font-semibold text-sm">Upload Daily Photos</span>
                    </button>
                    <a href="{{ route('students') }}"
                       class="bg-white/10 hover:bg-white/20 transition-colors py-6 rounded-3xl flex flex-col items-center gap-2">
                        <i class="fas fa-user-graduate text-2xl"></i>
                        <span class="font-semibold text-sm">Manage Students</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function updateClock() {
                const now = new Date();
                const dateEl = document.getElementById('currentDate');
                const timeEl = document.getElementById('currentTime');

                const dateOptions = { weekday: 'short', month: 'short', day: 'numeric' };
                dateEl.textContent = now.toLocaleDateString('en-US', dateOptions);

                let hours = now.getHours();
                let minutes = now.getMinutes();
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                timeEl.textContent = `${hours}:${minutes < 10 ? '0' : ''}${minutes} ${ampm}`;
            }
            updateClock();
            setInterval(updateClock, 60000);

            if (typeof Echo !== 'undefined') {
                try {
                    Echo.channel('dashboard')
                        .listen('DashboardUpdated', (e) => {
                            const studentsEl = document.getElementById('studentsCount');
                            const teachersEl = document.getElementById('teachersCount');
                            if (studentsEl) studentsEl.textContent = e.studentsCount ?? studentsEl.textContent;
                            if (teachersEl) teachersEl.textContent = e.teachersCount ?? teachersEl.textContent;
                        });
                } catch (error) {
                    console.log('%c🔧 Echo realtime skipped (safe mode)', 'color:#10b981;font-weight:600');
                }
            }
        });
    </script>
</x-layout>
