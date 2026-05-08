{{-- resources/views/components/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'KidWatch' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        :root {
            --tw-color-sidebar: #081028;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        /* Nav Link Styling */
        .sidebar-link {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
        }

        .sidebar-link i {
            color: #3b82f6;
            transition: transform 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .sidebar-link:hover i {
            transform: scale(1.1);
            color: #60a5fa;
        }

        /* Active State */
        .sidebar-link.active {
            background-color: #003366;
            color: white;
            box-shadow: 0 10px 15px -3px rgb(0 51 102 / 0.3);
        }

        .sidebar-link.active i {
            color: white;
        }

        /* Custom Scrollbar for Sidebar */
        .main-content::-webkit-scrollbar {
            width: 4px;
        }
        
        .main-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-[#f4f7fe]">
    @php
        $user = Auth::user();
        $role = $user?->role ?? 'guest';
        $profile = $role === 'teacher'
            ? ($user?->teacher ?? (object)['first_name' => 'User'])
            : ($user?->guardian ?? (object)['first_name' => 'User']);
        $displayName = trim(($profile->first_name ?? 'User') . ' ' . ($profile->last_name ?? ''));
        $initials = strtoupper(substr($profile->first_name ?? 'U', 0, 1) . substr($profile->last_name ?? 'U', 0, 1));
    @endphp

    <div class="flex min-h-screen">
        {{-- Fixed Sidebar --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-[#081028] shadow-2xl flex flex-col 
                      -translate-x-full lg:translate-x-0 lg:static lg:w-64 lg:shadow-none
                      transition-transform duration-300 ease-in-out border-r border-white/5">

            <div class="px-8 py-10">
                <h1 class="text-xl font-black tracking-widest text-white uppercase">KidWatch</h1>
            </div>

            <nav class="flex-1 px-4 py-2 space-y-2 overflow-y-auto main-content">
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('guardians.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('guardians*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Guardians</span>
                </a>

                <a href="{{ route('students') ?? route('students.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('students*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>

                <a href="{{ route('progress') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('progress') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Progress</span>
                </a>

                <a href="{{ route('logs.index') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('logs.index') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>System Logs</span>
                </a>

                <a href="{{ route('guardians.trash') }}"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl font-medium
                          {{ request()->routeIs('guardians.trash') ? 'active' : '' }}">
                    <i class="fas fa-trash-alt !text-red-400"></i>
                    <span>Trash Bin</span>
                </a>
            </nav>

            <div class="p-6 border-t border-white/5 mt-auto">
                <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/5">
                    <div class="w-9 h-9 bg-white text-[#081028] rounded-full flex items-center justify-center font-bold shadow-md text-xs">
                        {{ $initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white text-sm truncate">{{ $displayName }}</p>
                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-tighter">
                            {{ $role === 'teacher' ? 'Teacher' : 'Guardian' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Confirm Logout?')"
                                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-red-400 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Page Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <div class="lg:hidden bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">
                <button onclick="toggleSidebar()" class="text-[#081028] p-2 -ml-2 rounded-xl hover:bg-slate-50">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <h1 class="text-lg font-black text-[#081028] uppercase tracking-tighter">KidWatch</h1>
                <div class="w-8 h-8 bg-[#081028] text-white rounded-full flex items-center justify-center font-bold text-[10px]">
                    {{ $initials }}
                </div>
            </div>

            <main class="flex-1 p-6 md:p-8 lg:p-10 main-content overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    
    {{-- Overlay for Mobile --}}
    <div onclick="toggleSidebar()" id="sidebar-overlay" class="hidden lg:hidden fixed inset-0 bg-black/60 z-40 backdrop-blur-sm transition-opacity duration-300"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>