<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Wise HRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' };</script>
    <style>
        @keyframes logoFloatIn {
            0% { opacity: 0; transform: translateY(12px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes cardRiseIn {
            0% { opacity: 0; transform: translateY(18px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .login-logo-animate {
            opacity: 0;
            animation: logoFloatIn 700ms ease-out forwards;
        }
        .login-card-animate {
            opacity: 0;
            animation: cardRiseIn 650ms ease-out 220ms forwards;
        }
    </style>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900">
    <div class="min-h-full flex flex-col items-center justify-center py-12 px-4">
        @php
            $loginLogo = \Modules\Settings\Services\SettingsService::get('company_logo');
            $loginLogoUrl = $loginLogo ? asset('storage/' . ltrim($loginLogo, '/')) : null;
        @endphp
        @if($loginLogoUrl)
            <img src="{{ $loginLogoUrl }}" alt="Logo" class="h-10 object-contain max-w-xs mb-6 login-logo-animate">
        @else
            <h1 class="text-2xl font-bold text-white mb-6 login-logo-animate">Wise HRM</h1>
        @endif
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg max-w-md w-full">{{ session('success') }}</div>
        @endif
        <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8 login-card-animate">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-lg border border-slate-300/80 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300/80 dark:border-slate-600 dark:bg-slate-800 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 dark:border-slate-600">
                        <span class="ml-2 text-xs text-slate-600 dark:text-slate-400">Remember me on this device</span>
                    </label>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">Secure HR portal</span>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md hover:shadow-lg transition-shadow">
                    Sign in
                </button>
            </form>
        </div>
        <p class="mt-8 text-xs text-slate-500 dark:text-slate-400">Developed by M H Morshed</p>
    </div>
</body>
</html>
