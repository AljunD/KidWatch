<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; 
            overflow: hidden; 
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        .modal-highlight {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="h-full w-full flex items-center justify-center p-0 md:p-12 overflow-hidden" 
      x-data="{ 
        loading: false, 
        email: '', 
        password: '', 
        showError: false,
        validate() {
            if (!this.email || !this.password) {
                this.showError = true;
                return false;
            }
            this.showError = false;
            this.loading = true;
            return true;
        }
      }">

    <div class="bg-white w-full max-w-6xl h-full md:h-[min(800px,95vh)] md:max-h-[800px] md:rounded-[3rem] overflow-hidden modal-highlight flex flex-col md:flex-row border border-gray-200 relative">
        
        <div class="w-full md:w-1/2 p-8 md:p-16 lg:p-24 flex flex-col bg-white overflow-y-auto no-scrollbar relative" x-data="{ showPassword: false }">
            
            <div class="flex-grow flex flex-col justify-center">
                
                <div class="mb-10">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2 tracking-tighter">Welcome Back</h1>
                    <p class="text-gray-400 text-lg font-light">Please sign in with your credentials</p>
                </div>

                <form method="POST" action="{{ route('login') }}" @submit.prevent="if(validate()) $el.submit()" class="space-y-5" novalidate>
                    @csrf

                    <div class="group">
                        <label for="email" class="text-sm font-semibold text-gray-500 ml-1">Email Address</label>
                        <input type="email" id="email" name="email" x-model="email"
                               placeholder="your@email.com"
                               :class="showError && !email ? 'border-red-500 bg-red-50/50' : 'border-gray-100 bg-gray-50/50 focus:border-gray-900 focus:ring-4 focus:ring-gray-50'"
                               class="w-full px-5 py-4 rounded-2xl border transition-all outline-none text-base">
                        
                        <template x-if="showError && !email">
                            <p class="text-red-500 text-xs mt-1.5 ml-1 flex items-center gap-1.5">
                                <i class="fas fa-circle-exclamation"></i> Please enter your email address
                            </p>
                        </template>
                    </div>

                    <div class="group">
                        <label for="password" class="text-sm font-semibold text-gray-500 ml-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password"
                                   placeholder="••••••••"
                                   :class="showError && !password ? 'border-red-500 bg-red-50/50' : 'border-gray-100 bg-gray-50/50 focus:border-gray-900 focus:ring-4 focus:ring-gray-50'"
                                   class="w-full px-5 py-4 rounded-2xl border transition-all outline-none text-base">
                            
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-900 transition-colors">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>

                        <template x-if="showError && !password">
                            <p class="text-red-500 text-xs mt-1.5 ml-1 flex items-center gap-1.5">
                                <i class="fas fa-circle-exclamation"></i> Please enter your password
                            </p>
                        </template>
                    </div>

                    <div class="flex items-center justify-between text-sm py-2">
                        <label class="flex items-center gap-2 text-gray-400 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-gray-300 text-gray-900 focus:ring-0">
                            <span class="group-hover:text-gray-900 transition-colors">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-medium text-gray-400 hover:text-gray-900 underline underline-offset-8 decoration-transparent hover:decoration-gray-200 transition-all text-xs">Forgot Password?</a>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4.5 rounded-2xl transition-all shadow-xl shadow-gray-200 active:scale-[0.98] h-14 flex items-center justify-center gap-2">
                            <span x-show="!loading">Sign in</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <i class="fas fa-circle-notch fa-spin text-xs"></i> Authenticating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="hidden md:block w-1/2 relative">
            <img src="https://i.pinimg.com/736x/5e/d8/32/5ed8325294e4f4c6155499097e677c90.jpg" 
                 class="absolute inset-0 w-full h-full object-cover" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-l from-transparent via-white/5 to-white/20"></div>
        </div>
    </div>

</body>
</html>