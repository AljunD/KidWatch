<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidWatch | Forgot Password</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            background: linear-gradient(135deg, #f0f7ff, #dceeff);
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-10">
            <h2 class="text-4xl font-extrabold text-[#003366] uppercase tracking-tight">KidWatch</h2>
            <p class="mt-2 text-sm text-gray-500">Forgot Your Password?</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl border border-gray-100">
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

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

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-[#003366] mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required autofocus
                           placeholder="yourname@school.com"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-100 bg-gray-50 text-gray-700 outline-none">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-[#003e6d] hover:bg-[#002d50] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg active:scale-[0.98] flex items-center justify-center space-x-2">
                        <span>Send Reset Link</span>
                        <i class="fas fa-paper-plane text-sm opacity-70"></i>
                    </button>
                </div>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login.form') }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                   ← Back to Login
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-xs text-gray-400 font-medium">
            Authorized Personnel Only &mdash; © {{ date('Y') }} KidWatch System
        </p>
    </div>

</body>
</html>
