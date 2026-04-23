{{-- resources/views/components/layout.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'KidWatch' }}</title>

    {{-- Tailwind CSS (Vite / compiled in production - you already have this in your app) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome 6 (matches your existing pages) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap');

        :root {
            --tw-color-primary: #003366;
        }

        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-link.active {
            background-color: #003366;
            color: white;
            box-shadow: 0 10px 15px -3px rgb(0 51 102 / 0.2);
        }

        .main-content {
            scrollbar-width: thin;
            scrollbar-color: #0077cc #f1f5f9;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans">
    @php
        $user = Auth::user();
        $role = $user?->role ?? 'guest';
        $profile = $role === 'teacher'
            ? ($user?->teacher ?? (object)['first_name' => 'User'])
            : ($user?->guardian ?? (object)['first_name' => 'User']);
        $displayName = trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? ''));
        $initials = strtoupper(substr($profile->first_name ?? '', 0, 1) . substr($profile->last_name ?? '', 0, 1));
    @endphp

    <div class="flex min-h-screen bg-slate-100">

        {{-- SIDEBAR NAVIGATION --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl flex flex-col 
                      -translate-x-full lg:translate-x-0 lg:static lg:w-64 lg:shadow-none
                      transition-transform duration-300 ease-in-out border-r border-slate-200">

            {{-- Logo Header --}}
            <div class="px-6 py-8 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#003366] text-white rounded-3xl flex items-center justify-center text-3xl shadow-inner flex-shrink-0">
                    📖
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-[-2px] text-[#003366]">KidWatch</h1>
                    <p class="text-[10px] font-bold uppercase text-slate-400 -mt-1 tracking-[1px]">Progress • Together</p>
                </div>
            </div>

            {{-- Navigation Links --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link flex items-center gap-3 px-5 py-4 text-slate-700 hover:bg-slate-100 rounded-3xl font-semibold
                          {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Students (for student.blade.php) --}}
                <a href="{{ route('students') ?? route('students.index') }}"
                   class="sidebar-link flex items-center gap-3 px-5 py-4 text-slate-700 hover:bg-slate-100 rounded-3xl font-semibold
                          {{ request()->routeIs('students*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate w-5 h-5"></i>
                    <span>Students</span>
                </a>

                {{-- Progress --}}
                <a href="{{ route('progress') }}"
                   class="sidebar-link flex items-center gap-3 px-5 py-4 text-slate-700 hover:bg-slate-100 rounded-3xl font-semibold
                          {{ request()->routeIs('progress') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5 h-5"></i>
                    <span>Progress Log</span>
                </a>
                 {{-- Recommendations --}}
                <a href="{{ route('recommendation') }}"
                   class="sidebar-link flex items-center gap-3 px-5 py-4 text-slate-700 hover:bg-slate-100 rounded-3xl font-semibold
                          {{ request()->routeIs('recommendation') ? 'active' : '' }}">
                    <i class="fas fa-lightbulb w-5 h-5"></i>
                    <span>Recommendation</span>
                </a>

                {{-- Divider --}}
                <div class="h-px bg-slate-100 my-6 mx-5"></div>
            </nav>
            {{-- Trash (Student Records) --}}
            <a href="{{ route('students.trash') }}"
            class="sidebar-link flex items-center gap-3 px-5 py-4 text-slate-700 hover:bg-slate-100 rounded-3xl font-semibold
            {{ request()->routeIs('students.trash') ? 'active' : '' }}">
                <i class="fas fa-trash-alt w-5 h-5 text-red-500"></i>
                <span>Trash</span>
            </a>

            {{-- Sidebar Footer - User Info --}}
            <div class="p-6 border-t border-slate-100 mt-auto">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-[#003366] text-white rounded-3xl flex items-center justify-center font-black text-lg shadow-inner">
                        {{ $initials ?: '👤' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#003366] truncate">{{ $displayName ?: 'Welcome' }}</p>
                        <p class="text-xs text-slate-400 uppercase font-medium">
                            {{ $role === 'teacher' ? 'Teacher' : 'Parent / Guardian' }}
                        </p>
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Log out of KidWatch?')"
                                class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-3xl">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 lg:ml-0">

            {{-- MOBILE TOP BAR (Hamburger + Logo + User) --}}
            <div class="lg:hidden bg-white border-b border-slate-200 px-5 py-4 flex items-center justify-between sticky top-0 z-40 shadow-sm">

                {{-- Hamburger + Logo --}}
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()"
                            class="text-[#003366] p-2 -ml-2 hover:bg-slate-100 rounded-3xl">
                        <i class="fas fa-bars text-3xl"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-[#003366] text-white rounded-3xl flex items-center justify-center text-2xl">
                            📖
                        </div>
                        <h1 class="text-2xl font-black tracking-[-1px] text-[#003366]">KidWatch</h1>
                    </div>
                </div>

                {{-- Mobile User Info --}}
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-[#003366]">{{ $displayName ?: 'Hi there' }}</p>
                        <p class="text-[10px] text-slate-400 uppercase">{{ $role === 'teacher' ? 'Teacher' : 'Parent' }}</p>
                    </div>
                    <div class="w-8 h-8 bg-[#003366] text-white rounded-3xl flex items-center justify-center font-black shadow-inner">
                        {{ $initials ?: '👤' }}
                    </div>
                </div>
            </div>

            {{-- PAGE CONTENT SLOT --}}
            <main class="flex-1 p-6 md:p-8 lg:p-10 main-content overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- MOBILE OVERLAY --}}
    <div onclick="if(event.target.id === 'sidebar-overlay') toggleSidebar()"
         id="sidebar-overlay"
         class="hidden lg:hidden fixed inset-0 bg-black/60 z-40 backdrop-blur-sm"></div>

    {{-- Tailwind script (only needed if you are not using Vite / compiled Tailwind) --}}
    <script>
        function initializeTailwind() {
            tailwind.config = {
                content: [],
                theme: {
                    extend: {}
                }
            }
        }

        {{-- Sidebar Toggle (Hamburger) --}}
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar')
            const overlay = document.getElementById('sidebar-overlay')

            if (sidebar.classList.contains('-translate-x-full')) {
                // Open
                sidebar.classList.remove('-translate-x-full')
                overlay.classList.remove('hidden')
                overlay.classList.add('block')
            } else {
                // Close
                sidebar.classList.add('-translate-x-full')
                overlay.classList.add('hidden')
                overlay.classList.remove('block')
            }
        }

        {{-- Close sidebar when clicking any nav link on mobile --}}
        document.addEventListener('DOMContentLoaded', () => {
            initializeTailwind()

            const links = document.querySelectorAll('#sidebar a')
            links.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) { // lg breakpoint
                        setTimeout(() => {
                            const sidebar = document.getElementById('sidebar')
                            const overlay = document.getElementById('sidebar-overlay')
                            sidebar.classList.add('-translate-x-full')
                            overlay.classList.add('hidden')
                            overlay.classList.remove('block')
                        }, 150)
                    }
                })
            })

            console.log('%c✅ KidWatch sidebar navigation ready (fully responsive + mobile hamburger)', 'color:#003366; font-size:13px; font-weight:700')
        })
    </script>
</body>
</html>
