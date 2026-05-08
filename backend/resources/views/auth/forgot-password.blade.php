<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KidWatch | Forgot Password</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
            background-color: #000; /* Fallback */
        }

        /* Blurred Background Layer */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), 
                              url('https://wallpapers.com/images/high/kids-background-phhsee8es7zh9v2w.webp');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            transform: scale(1.1); /* Prevents white edges from blur */
            z-index: -1;
        }

        .modal-highlight {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body x-data="{ 
    email: '', 
    showError: false,
    validate() {
        if (!this.email) {
            this.showError = true;
            return false;
        }
        this.showError = false;
        return true;
    }
}">

    <div class="bg-white/95 backdrop-blur-md w-full max-w-[450px] p-10 rounded-[40px] modal-highlight text-center mx-4 relative">
        
        <div class="mb-8">
            {{-- Key Icon Removed --}}
            <h1 class="text-3xl font-black text-gray-900 mb-2 tracking-tight uppercase">Forgot Password?</h1>
            <p class="text-gray-400 text-sm font-medium">Enter your email to reset your password</p>
        </div>

        <form @submit.prevent="if(validate()) $el.submit()" method="POST" action="{{ route('password.email') }}" class="space-y-6 text-left" novalidate>
            @csrf

            <div class="group">
                <label for="email" class="text-[10px] font-black text-gray-500 mb-2 ml-1 block uppercase tracking-widest">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <i class="far fa-envelope"></i>
                    </span>
                    <input type="email" id="email" x-model="email" name="email"
                           placeholder="Enter your email"
                           :class="showError ? 'border-red-500 bg-red-50/30' : 'border-gray-100 bg-gray-50/50 focus:border-gray-900 focus:ring-4 focus:ring-gray-50'"
                           class="w-full pl-11 pr-4 py-4 rounded-2xl border transition-all outline-none text-base placeholder:text-gray-300 font-medium">
                </div>

                <template x-if="showError">
                    <div class="mt-2 flex items-start gap-2 text-red-500 animate-in fade-in slide-in-from-top-1 duration-200">
                        <i class="fas fa-exclamation-circle mt-0.5 text-xs"></i>
                        <p class="text-[12px] font-bold">Please enter a valid email address</p>
                    </div>
                </template>
            </div>

            <div class="pt-2">
                <button type="submit" 
                    class="w-full bg-[#1a1a1a] hover:bg-black text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-gray-200 active:scale-[0.98] h-14 uppercase text-xs tracking-widest">
                    Reset Password
                </button>
            </div>
        </form>

        <div class="mt-8 border-t border-gray-100 pt-6">
            <a href="{{ route('login') }}" class="text-xs font-black text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-widest flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left text-[10px]"></i>
                Back to Login
            </a>
        </div>
    </div>

</body>
</html>