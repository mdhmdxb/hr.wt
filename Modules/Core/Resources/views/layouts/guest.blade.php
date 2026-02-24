{{-- Minimal layout for public pages (e.g. verification) - no auth required --}}
@php
    $ts = \Modules\Settings\Services\SettingsService::class;
    $guestThemePrimary = $ts::get('primary_color', '#4f46e5');
    $guestThemeFont = $ts::allowedFontValue($ts::get('font_family', 'system-ui'));
    $guestThemeHeadingFont = $ts::allowedFontValue($ts::get('heading_font', 'system-ui'));
@endphp
<!DOCTYPE html>
@php $isDark = request()->cookie('theme') === 'dark'; @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full {{ $isDark ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ route('app.favicon') }}" type="image/png" sizes="any">
    <title>@yield('title', 'Verify') - Wise HRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' };</script>
    <style>
        :root { --wise-primary: {{ $guestThemePrimary }}; --wise-font: {!! \Modules\Settings\Services\SettingsService::fontKeyToCss($guestThemeFont) !!}; --wise-heading-font: {!! \Modules\Settings\Services\SettingsService::fontKeyToCss($guestThemeHeadingFont) !!}; }
        body { font-family: var(--wise-font); }
        h1, h2, .wise-heading { font-family: var(--wise-heading-font); }
    </style>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <div class="min-h-full flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md mb-6 text-center">
            @php $logo = \Modules\Settings\Services\SettingsService::get('company_logo'); @endphp
            @if($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
                <img src="{{ route('app.logo') }}" alt="Logo" class="h-10 mx-auto object-contain">
            @else
                <span class="text-xl font-bold" style="color: var(--wise-primary);">Wise HRM</span>
            @endif
        </div>
        @yield('content')
    </div>
</body>
</html>
