<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidWatch | Secure Login</title>

    <!-- Content Security Policy -->
    <meta http-equiv="Content-Security-Policy"
          content="default-src 'self';
                   script-src 'self' https://cdn.tailwindcss.com https://unpkg.com;
                   style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com;
                   font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com;
                   img-src 'self' data:;
                   object-src 'none';
                   base-uri 'self';
                   form-action 'self';
                   frame-ancestors 'none';
                   upgrade-insecure-requests;">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Fonts -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            background: linear-gradient(135deg, #f0f7ff, #dceeff);
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md" x-data="{ showPassword: false }">
        <!-- Logo / Title -->
        <div class="text-center mb-10">
            <h2 class="text-4xl font-extrabold text-[#003366] uppercase tracking-tight">KidWatch</h2>
            <p class="mt-2 text-sm text-gray-500">Secure Access Portal</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl border border-gray-100">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-[#003366] mb-1">Welcome Back</h1>
                <p class="text-gray-500 text-sm">Sign in with your credentials</p>
            </div>

            <!-- Success Messages -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-[#003366] mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus maxlength="255" autocomplete="username"
                               placeholder="yourname@school.com"
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 bg-gray-50 text-gray-700 transition-all outline-none">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-[#003366] mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password"
                               required minlength="8" maxlength="255" autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-11 pr-12 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 bg-gray-50 text-gray-700 transition-all outline-none">
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-[#003e6d] hover:bg-[#002d50] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg active:scale-[0.98] flex items-center justify-center space-x-2">
                        <span>Sign In</span>
                        <i class="fas fa-arrow-right text-sm opacity-70"></i>
                    </button>
                </div>
                <!-- Forgot Password -->
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Forgot your password?
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-xs text-gray-400 font-medium">
            Authorized Personnel Only &mdash; © {{ date('Y') }} KidWatch System
        </p>
    </div>

</body>
</html>
