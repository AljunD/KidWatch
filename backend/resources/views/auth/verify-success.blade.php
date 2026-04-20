<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidWatch | Email Verified</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <p class="mt-2 text-sm text-gray-500">Email Verified Successfully</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl p-8 md:p-10 shadow-xl border border-gray-100 text-center">
            <h3 class="text-xl font-semibold text-green-600 mb-4">🎉 Your Email Has Been Verified!</h3>
            <p class="text-gray-600 mb-6">
                Thank you for confirming your email address. Your KidWatch account is now fully active.
            </p>

            <!-- Call to Action -->
            <div class="text-center">
                <a href="{{ route('dashboard') }}"
                   class="bg-[#003e6d] hover:bg-[#002d50] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg inline-block">
                   Go to Dashboard
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-xs text-gray-400 font-medium">
            © {{ date('Y') }} KidWatch System — Secure Access Only
        </p>
    </div>

</body>
</html>
