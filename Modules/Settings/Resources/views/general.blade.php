@extends('core::layouts.app')

@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<div class="max-w-3xl space-y-8">
    {{-- General / Company --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Company & General</h2>
        <form method="POST" action="{{ route('settings.general.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $company_name) }}" required
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Logo</label>
                <input type="file" name="company_logo" accept="image/*"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Favicon</label>
                <input type="file" name="favicon" accept="image/x-icon,image/png,image/gif"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Small icon for browser tab (PNG/ICO, max 512KB). Leave empty to use app logo or wt-logo.png.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address</label>
                <textarea name="company_address" rows="2" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">{{ old('company_address', $company_address) }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $company_phone) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $company_email) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $timezone) }}" placeholder="UTC"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date format</label>
                    <input type="text" name="date_format" value="{{ old('date_format', $date_format) }}" placeholder="Y-m-d"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $currency) }}" placeholder="USD"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save</button>
        </form>
    </section>

    {{-- Appearance (theme variables used app-wide) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Appearance &amp; theme</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">These values are applied across the app: buttons, links, sidebar, and typography.</p>
        <form method="POST" action="{{ route('settings.appearance.store') }}" class="space-y-6">
            @csrf
            @php
                $svc = \Modules\Settings\Services\SettingsService::class;
                $primary = old('primary_color', $svc::get('primary_color', '#4f46e5'));
                $secondary = old('secondary_color', $svc::get('secondary_color', '#6366f1'));
                $accent = old('accent_color', $svc::get('accent_color', '#818cf8'));
                $linkColor = old('link_color', $svc::get('link_color', '#4f46e5'));
                $buttonBg = old('button_bg', $svc::get('button_bg', '#4f46e5'));
                $sidebarActiveBg = old('sidebar_active_bg', $svc::get('sidebar_active_bg', 'rgba(79, 70, 229, 0.1)'));
                $sidebarActiveText = old('sidebar_active_text', $svc::get('sidebar_active_text', '#4f46e5'));
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Primary color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ $primary }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="primary_color" value="{{ $primary }}" placeholder="#4f46e5"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Secondary color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ $secondary }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="secondary_color" value="{{ $secondary }}" placeholder="#6366f1"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Accent color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ $accent }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="accent_color" value="{{ $accent }}" placeholder="#818cf8"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Link color</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $linkColor) ? $linkColor : '#4f46e5' }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="link_color" value="{{ $linkColor }}" placeholder="#4f46e5"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Button background</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ $buttonBg }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="button_bg" value="{{ $buttonBg }}" placeholder="#4f46e5"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sidebar active text</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $sidebarActiveText) ? $sidebarActiveText : '#4f46e5' }}" oninput="this.nextElementSibling.value=this.value" class="h-10 w-14 rounded border border-slate-300 dark:border-slate-600 cursor-pointer">
                        <input type="text" name="sidebar_active_text" value="{{ $sidebarActiveText }}" placeholder="#4f46e5"
                            class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sidebar active background (e.g. rgba(79,70,229,0.1))</label>
                    <input type="text" name="sidebar_active_bg" value="{{ $sidebarActiveBg }}" placeholder="rgba(79, 70, 229, 0.1)"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Border radius (e.g. 0.5rem)</label>
                    <input type="text" name="border_radius" value="{{ old('border_radius', $svc::get('border_radius', '0.5rem')) }}" placeholder="0.5rem"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            @php
                $allowedFonts = \Modules\Settings\Services\SettingsService::allowedFontFamilies();
                $currentBody = old('font_family', $svc::allowedFontValue($svc::get('font_family', 'system-ui')));
                $currentHeading = old('heading_font', $svc::allowedFontValue($svc::get('heading_font', 'system-ui')));
                $currentBodyCss = \Modules\Settings\Services\SettingsService::fontKeyToCss($currentBody);
                $currentHeadingCss = \Modules\Settings\Services\SettingsService::fontKeyToCss($currentHeading);
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{
                bodyFont: @json($currentBody),
                headingFont: @json($currentHeading),
                bodyFontCss: @json($currentBodyCss),
                headingFontCss: @json($currentHeadingCss),
                updateBodyCss() { const sel = $refs.bodyFontSelect; const opt = sel.options[sel.selectedIndex]; this.bodyFontCss = opt ? (opt.getAttribute('data-font-css') || this.bodyFontCss) : this.bodyFontCss; },
                updateHeadingCss() { const sel = $refs.headingFontSelect; const opt = sel.options[sel.selectedIndex]; this.headingFontCss = opt ? (opt.getAttribute('data-font-css') || this.headingFontCss) : this.headingFontCss; }
            }" x-init="updateBodyCss(); updateHeadingCss();">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Body font</label>
                    <select name="font_family" x-ref="bodyFontSelect" x-model="bodyFont" @change="updateBodyCss()" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                        @foreach($allowedFonts as $key => $label)
                            <option value="{{ $key }}" {{ $key === $currentBody ? 'selected' : '' }} data-font-css="{{ e($svc::fontKeyToCss($key)) }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600" :style="'font-family: ' + bodyFontCss">The quick brown fox jumps over the lazy dog. 0123456789</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Heading font</label>
                    <select name="heading_font" x-ref="headingFontSelect" x-model="headingFont" @change="updateHeadingCss()" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                        @foreach($allowedFonts as $key => $label)
                            <option value="{{ $key }}" {{ $key === $currentHeading ? 'selected' : '' }} data-font-css="{{ e($svc::fontKeyToCss($key)) }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-600 text-lg font-semibold" :style="'font-family: ' + headingFontCss">The quick brown fox jumps over the lazy dog. 0123456789</p>
                </div>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Preview updates as you pick a font. Save to apply app-wide.</p>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save appearance</button>
        </form>
    </section>

    {{-- Email / SMTP --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Email (SMTP)</h2>
        <form method="POST" action="{{ route('settings.mail.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', \Modules\Settings\Services\SettingsService::get('mail_host')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Port</label>
                    <input type="text" name="mail_port" value="{{ old('mail_port', \Modules\Settings\Services\SettingsService::get('mail_port')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', \Modules\Settings\Services\SettingsService::get('mail_username')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', \Modules\Settings\Services\SettingsService::get('mail_password')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Encryption (tls/ssl/null)</label>
                    <input type="text" name="mail_encryption" value="{{ old('mail_encryption', \Modules\Settings\Services\SettingsService::get('mail_encryption')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">From address</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', \Modules\Settings\Services\SettingsService::get('mail_from_address')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">From name</label>
                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', \Modules\Settings\Services\SettingsService::get('mail_from_name')) }}"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save</button>
        </form>
    </section>
</div>
@endsection
