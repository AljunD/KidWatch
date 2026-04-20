{{-- resources/views/components/layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'KidWatch' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* New Modern Sidebar */
        .modern-sidebar {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(16px);
            box-shadow: 10px 0 30px -10px rgb(0 51 102 / 0.1);
            border-right: 1px solid rgba(0, 51, 102, 0.08);
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        [x-cloak] { display: none !important; }

        /* Active nav pill style (matches new dashboard) */
        .nav-link-active {
            background: linear-gradient(90deg, #003366, #0077cc);
            color: white;
            box-shadow: 0 10px 15px -3px rgb(0 51 102 / 0.2);
        }
    </style>
</head>
<body class="min-h-screen text-slate-900" x-data="{ sidebarOpen: false }" @keydown.escape="sidebarOpen = false">

    {{-- Mobile Top Bar (unchanged) --}}
    <header class="lg:hidden bg-white/70 backdrop-blur-lg border-b border-slate-200/50 p-4 flex items-center justify-between fixed top-0 w-full z-[60]">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="text-[#003366] bg-blue-50 w-10 h-10 rounded-3xl flex items-center justify-center active:scale-90 transition-all">
                <i class="fas fa-bars-staggered"></i>
            </button>
            <span class="text-lg font-extrabold text-[#003366] tracking-tighter uppercase italic">Kidwatch</span>
        </div>

        @auth
        <div class="w-10 h-10 bg-[#003366] rounded-3xl flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-blue-900/20">
            {{ substr(Auth::user()->email, 0, 1) }}
        </div>
        @endauth
    </header>

    {{-- Backdrop Overlay (unchanged) --}}
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition opacity-ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity-ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-[#001a33]/60 z-[70] lg:hidden backdrop-blur-sm"></div>

    {{-- NEW SIDE NAV --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 w-72 modern-sidebar p-6 flex flex-col transition-transform duration-300 ease-in-out z-[80] lg:m-4 lg:h-[calc(100vh-2rem)] lg:rounded-3xl overflow-y-auto no-scrollbar">

        {{-- Branding - fresh & modern --}}
        <div class="flex justify-between items-center mb-10 px-2">
            <div class="flex items-center gap-4">
                <div class="bg-[#003366] w-12 h-12 rounded-3xl flex items-center justify-center shadow-xl shadow-blue-900/30 rotate-6">
                    <i class="fas fa-child-reaching text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-[#003366] tracking-[-1px] uppercase italic">Kidwatch</h2>
                    <p class="text-[10px] font-black text-[#003366]/50 uppercase tracking-[1.5px] -mt-1">Daycare Pro</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-[#003366] bg-white w-10 h-10 rounded-3xl hover:bg-slate-100 transition-colors flex items-center justify-center shadow-sm">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Nav Links - new pill style to match dashboard --}}
        <nav class="flex-1 space-y-2">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Overview'],
                    ['route' => 'students', 'icon' => 'fa-user-graduate', 'label' => 'Students'],
                    ['route' => 'students.progress', 'icon' => 'fa-star', 'label' => 'Progress Log'],
                ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-4 px-6 py-5 rounded-3xl transition-all duration-300 group text-base font-semibold
                      {{ request()->routeIs($item['route']) ? 'nav-link-active' : 'text-[#003366]/70 hover:bg-white hover:shadow hover:text-[#003366]' }}">
                <i class="fas {{ $item['icon'] }} w-6 text-xl"></i>
                <span>{{ $item['label'] }}</span>

                @if(request()->routeIs($item['route']))
                    <div class="ml-auto w-3 h-3 bg-white/30 rounded-full animate-ping"></div>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- Profile Card - redesigned to match dashboard aesthetic --}}
        <div class="mt-auto pt-8">
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    @php
                        $user = Auth::user();
                        $displayName = in_array($user->role, ['admin', 'teacher'])
                            ? ($user->teacher->first_name ?? 'Staff')
                            : ($user->guardian->first_name ?? 'Parent');
                    @endphp

                    <div class="w-11 h-11 bg-gradient-to-br from-[#003366] to-blue-700 text-white rounded-3xl flex items-center justify-center font-black text-2xl shadow-inner">
                        {{ substr($displayName, 0, 1) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-[#003366] truncate">{{ $displayName }}</p>
                        <span class="inline-block text-[10px] font-black uppercase tracking-widest bg-blue-100 text-[#003366] px-3 py-px rounded-3xl">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-8">
                    <a href="{{ route('students.trash') }}"
                       class="flex items-center justify-center gap-2 bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-700 py-4 rounded-3xl transition-all text-sm font-semibold">
                        <i class="fas fa-trash-can"></i>
                        <span>Trash</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-[#003366] hover:bg-red-600 text-white py-4 rounded-3xl transition-all text-sm font-semibold active:scale-95">
                            <i class="fas fa-power-off"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content Area - updated for new sidebar width --}}
    <main class="lg:ml-72 transition-all duration-300">
        <div class="p-4 sm:p-8 lg:p-12 pt-28 lg:pt-12 min-h-screen">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </div>
    </main>

</body>
</html>
