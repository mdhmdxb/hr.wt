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
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Country</label>
                <select name="company_country" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 max-w-xs">
                    <option value="">— Select country —</option>
                    @foreach(\Modules\Core\Helpers\CountryList::codes() as $code => $name)
                    <option value="{{ $code }}" {{ old('company_country', $company_country ?? '') == $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Used for public holidays and calendar (e.g. UAE, Saudi Arabia).</p>
            </div>
            <div class="pt-4 mt-2 border-t border-dashed border-slate-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Calendar / holiday API (optional)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">
                    These fields are placeholders only. The app does not call any external calendar/holiday API yet, but you can store credentials here for a future integration.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">API base URL</label>
                        <input type="text" name="calendar_api_url" value="{{ old('calendar_api_url', $calendarApiUrl) }}"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2"
                            placeholder="https://api.example.com/holidays">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">API key / token</label>
                        <input type="text" name="calendar_api_key" value="{{ old('calendar_api_key', $calendarApiKey) }}"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2"
                            placeholder="sk_live_xxx or similar">
                    </div>
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('settings.mail.store') }}" class="space-y-4 lg:col-span-2">
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
            <form method="POST" action="{{ route('settings.mail.test') }}" class="space-y-3">
                @csrf
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Send test email to</label>
                <input type="email" name="test_email" value="{{ old('test_email') }}" placeholder="you@example.com"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Use this to confirm that your SMTP settings above work correctly.</p>
                <button type="submit" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    Send test email
                </button>
            </form>
        </div>
    </section>

    {{-- Email – IMAP (Incoming) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Email – IMAP (Incoming)</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Configure IMAP to read incoming emails (e.g. for syncing or processing replies). Same credentials as SMTP are often used.</p>
        <form method="POST" action="{{ route('settings.imap.store') }}" class="space-y-4 max-w-2xl">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IMAP Host</label>
                    <input type="text" name="imap_host" value="{{ old('imap_host', \Modules\Settings\Services\SettingsService::get('imap_host')) }}" placeholder="imap.example.com"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">IMAP Port</label>
                    <input type="text" name="imap_port" value="{{ old('imap_port', \Modules\Settings\Services\SettingsService::get('imap_port')) }}" placeholder="993"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label>
                    <input type="text" name="imap_username" value="{{ old('imap_username', \Modules\Settings\Services\SettingsService::get('imap_username')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                    <input type="password" name="imap_password" value="{{ old('imap_password', \Modules\Settings\Services\SettingsService::get('imap_password')) }}"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Encryption (tls / ssl / null)</label>
                <input type="text" name="imap_encryption" value="{{ old('imap_encryption', \Modules\Settings\Services\SettingsService::get('imap_encryption')) }}" placeholder="ssl"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 max-w-xs">
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save IMAP settings</button>
        </form>
    </section>

    {{-- My signature (management/admins – used for approvals and letters) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">My signature</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Upload your signature image. It can be used on leave approval letters and other documents. PNG or JPG, max 2MB.</p>
        @if(!empty($signatureUrl) || !empty($currentUserSignaturePath))
        <p class="text-sm text-slate-600 dark:text-slate-300 mb-2">Current signature:</p>
        <img src="{{ $signatureUrl ?? asset('storage/' . ltrim($currentUserSignaturePath, '/')) }}" alt="Your signature" class="max-h-20 border border-slate-200 dark:border-slate-600 rounded-lg mb-3">
        @endif
        <form method="POST" action="{{ route('settings.signature.store') }}" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="file" name="signature" accept="image/png,image/jpeg,image/jpg" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">{{ isset($currentUserSignaturePath) && $currentUserSignaturePath ? 'Replace signature' : 'Upload signature' }}</button>
        </form>
    </section>

    {{-- Company stamp (HR/Admin – used on letters/documents) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Company stamp</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Upload the company stamp image. It will be used on official letters and documents. PNG or JPG, max 2MB.</p>
        @if(!empty($companyStampUrl) || !empty($companyStampPath))
        <p class="text-sm text-slate-600 dark:text-slate-300 mb-2">Current stamp:</p>
        <img src="{{ $companyStampUrl ?? asset('storage/' . ltrim($companyStampPath, '/')) }}" alt="Company stamp" class="max-h-24 border border-slate-200 dark:border-slate-600 rounded-lg mb-3">
        @endif
        <form method="POST" action="{{ route('settings.company-stamp.store') }}" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="file" name="company_stamp" accept="image/png,image/jpeg,image/jpg" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">{{ isset($companyStampPath) && $companyStampPath ? 'Replace company stamp' : 'Upload company stamp' }}</button>
        </form>
    </section>

    {{-- Letters & documents: stamp/signature per document type + footer text --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Letters &amp; documents</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Choose on which letters or documents to show the company stamp and signatory signatures. Set the footer text shown on all generated letters.</p>
        <form method="POST" action="{{ route('settings.document-display.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Show company stamp on</label>
                <div class="flex flex-wrap gap-4">
                    @foreach($documentTypes ?? [] as $key => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="document_stamp_on[]" value="{{ $key }}" {{ in_array($key, $documentStampOn ?? [], true) ? 'checked' : '' }}>
                        <span class="text-slate-700 dark:text-slate-300 text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Show signatory signature on</label>
                <div class="flex flex-wrap gap-4">
                    @foreach($documentTypes ?? [] as $key => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="document_signature_on[]" value="{{ $key }}" {{ in_array($key, $documentSignatureOn ?? [], true) ? 'checked' : '' }}>
                        <span class="text-slate-700 dark:text-slate-300 text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Footer text (all generated letters/documents)</label>
                <textarea name="letter_footer_text" rows="3" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm" placeholder="{{ \Modules\Settings\Http\Controllers\SettingsController::defaultLetterFooterText() }}">{{ old('letter_footer_text', $letterFooterText ?? '') }}</textarea>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">This line appears at the bottom of leave letters, payslips, and other generated documents. Leave empty to use the default.</p>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save letter & document settings</button>
        </form>
    </section>

    {{-- Working schedule overrides (e.g. Ramadan: reduced hours) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Working schedule overrides</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Set temporary periods with different working hours (e.g. Ramadan: 2 hours less). Employee break is set per person (Religion / Break minutes).</p>
        @if(isset($workingScheduleOverrides) && $workingScheduleOverrides->isNotEmpty())
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-600 text-left">
                        <th class="py-2 pr-2">Name</th>
                        <th class="py-2 pr-2">Scope</th>
                        <th class="py-2 pr-2">Start</th>
                        <th class="py-2 pr-2">End</th>
                        <th class="py-2 pr-2">Work start</th>
                        <th class="py-2 pr-2">Work end</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workingScheduleOverrides as $ov)
                    <tr class="border-b border-slate-100 dark:border-slate-700/50">
                        <td class="py-2 pr-2 font-medium">{{ $ov->name }}</td>
                        <td class="py-2 pr-2 text-slate-600 dark:text-slate-400">
                            @if($ov->employee_id) Employee: {{ $ov->employee->full_name ?? '—' }}
                            @elseif($ov->project_id) Project: {{ $ov->project->name ?? '—' }}
                            @elseif($ov->site_id) Site: {{ $ov->site->name ?? '—' }}
                            @elseif($ov->branch_id) Branch: {{ $ov->branch->name ?? '—' }}
                            @else Global
                            @endif
                        </td>
                        <td class="py-2 pr-2">{{ $ov->start_date->format('Y-m-d') }}</td>
                        <td class="py-2 pr-2">{{ $ov->end_date->format('Y-m-d') }}</td>
                        <td class="py-2 pr-2">{{ $ov->work_start ?? '—' }}</td>
                        <td class="py-2 pr-2">{{ $ov->work_end ?? '—' }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('settings.working-schedule-overrides.destroy', $ov) }}" class="inline" onsubmit="return confirm('Remove this override?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 text-xs hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <form method="POST" action="{{ route('settings.working-schedule-overrides.store') }}" class="space-y-3 max-w-2xl">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name (e.g. Ramadan)</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ramadan 2026" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Start date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">End date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Work start (e.g. 09:00)</label>
                    <input type="time" name="work_start" value="{{ old('work_start') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Work end (e.g. 15:00 for 2h less)</label>
                    <input type="time" name="work_end" value="{{ old('work_end') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
            </div>
            <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Scope (optional)</label>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Leave all empty for global. Set one to apply only to that branch, site, project, or employee.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Branch</label>
                        <select name="branch_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                            <option value="">— None —</option>
                            @foreach($branches ?? [] as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Site</label>
                        <select name="site_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                            <option value="">— None —</option>
                            @foreach($sites ?? [] as $s)
                            <option value="{{ $s->id }}" {{ old('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Project</label>
                        <select name="project_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                            <option value="">— None —</option>
                            @foreach($projects ?? [] as $p)
                            <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Employee</label>
                        <select name="employee_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1.5 text-sm">
                            <option value="">— None —</option>
                            @foreach($employees ?? [] as $e)
                            <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg mt-4">Add override</button>
        </form>
    </section>

    {{-- Dashboard layout: card visibility and order --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Dashboard layout</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Choose which cards appear on the dashboard and their order. Uncheck to hide a card. Lower order number appears first.
        </p>
        <form method="POST" action="{{ route('settings.dashboard-cards.store') }}" class="space-y-3">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-600 text-left">
                            <th class="py-2 pr-2 text-slate-600 dark:text-slate-400 font-medium">Show</th>
                            <th class="py-2 pr-2 text-slate-600 dark:text-slate-400 font-medium">Card</th>
                            <th class="py-2 text-slate-600 dark:text-slate-400 font-medium w-24">Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dashboardOrder = $dashboardCardOrder ?? array_keys($dashboardCardLabels ?? []);
                        @endphp
                        @foreach($dashboardCardLabels ?? [] as $key => $label)
                        @php
                            $pos = array_search($key, $dashboardOrder, true);
                            $orderNum = $pos !== false ? $pos + 1 : 99;
                        @endphp
                        <tr class="border-b border-slate-100 dark:border-slate-700/50">
                            <td class="py-2 pr-2">
                                <label class="sr-only">Show {{ $label }}</label>
                                <input type="checkbox" name="visible[{{ $key }}]" value="1" {{ in_array($key, $dashboardOrder, true) ? 'checked' : '' }}>
                            </td>
                            <td class="py-2 pr-2 text-slate-800 dark:text-slate-200">{{ $label }}</td>
                            <td class="py-2">
                                <input type="number" name="order[{{ $key }}]" value="{{ old('order.'.$key, $orderNum) }}" min="1" max="99" class="w-16 rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save dashboard layout</button>
        </form>
    </section>

    {{-- Letter templates (leave approval / cancellation) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Letter templates</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            These templates control the main text of leave approval and cancellation letters. Use the placeholders on the right
            to personalise messages. The preview in the PDF keeps the header, stamp, signatures, and footer.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('settings.general.store') }}" class="md:col-span-2 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave approval letter body</label>
                    <textarea name="template_leave_approval" rows="5" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">{{ old('template_leave_approval', $leaveApprovalTemplate ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Leave cancellation letter body</label>
                    <textarea name="template_leave_cancellation" rows="5" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">{{ old('template_leave_cancellation', $leaveCancellationTemplate ?? '') }}</textarea>
                </div>
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save templates</button>
            </form>
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 mb-2">Available placeholders</h3>
                <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                    @foreach($letterPlaceholders ?? [] as $ph => $label)
                    <li><code class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-[11px]">{{ $ph }}</code> – {{ $label }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Overtime rules --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Overtime rules</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Control how overtime hours are accepted from attendance. These rules are used when summarising working hours and overtime
            on batch attendance and employee self-attendance. They can also be used by payroll logic in future.
        </p>
        <form method="POST" action="{{ route('settings.overtime.store') }}" class="space-y-4 max-w-xl">
            @csrf
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="hidden" name="overtime_accept_partial" value="0">
                <input type="checkbox" name="overtime_accept_partial" value="1" {{ !empty($overtimeAcceptPartial) ? 'checked' : '' }} class="mt-1">
                <span class="text-sm text-slate-700 dark:text-slate-200">
                    Count broken minutes towards accepted overtime
                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">
                        When off, only full hours are accepted (e.g. 7h45m → 7h). When on, if the remaining minutes reach the threshold below,
                        they add one extra hour (e.g. 7h45m with 30‑minute threshold → 8h).
                    </span>
                </span>
            </label>
            <div class="max-w-xs">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Threshold for broken minutes (0–59)</label>
                <input type="number" name="overtime_partial_threshold" value="{{ $overtimePartialThreshold ?? 0 }}" min="0" max="59"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Example: 30 → if monthly overtime is 7h30m or more, accepted overtime becomes 8h; if 7h20m, it stays 7h.
                </p>
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save overtime rules</button>
        </form>
    </section>

    {{-- Payslip display (HR/Admin controls what appears on payslips) --}}
    <section class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Payslip display</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Choose which information is shown on employee payslips. Uncheck items to hide them.</p>
        <form method="POST" action="{{ route('settings.payslip.display.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($payslipDisplayKeys ?? [] as $key => $label)
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                    <input type="checkbox" name="payslip_display[]" value="{{ $key }}" {{ in_array($key, $payslipDisplay ?? [], true) ? 'checked' : '' }}>
                    <span class="text-slate-800 dark:text-slate-200 text-sm">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save payslip display</button>
        </form>
    </section>
</div>
@endsection
