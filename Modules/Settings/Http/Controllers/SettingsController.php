<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\SettingsService;

class SettingsController extends Controller
{
    public function general()
    {
        return view('settings::general', [
            'company_name' => SettingsService::get('company_name', ''),
            'company_address' => SettingsService::get('company_address', ''),
            'company_phone' => SettingsService::get('company_phone', ''),
            'company_email' => SettingsService::get('company_email', ''),
            'timezone' => SettingsService::get('timezone', config('app.timezone')),
            'date_format' => SettingsService::get('date_format', 'Y-m-d'),
            'currency' => SettingsService::get('currency', 'USD'),
        ]);
    }

    public function storeGeneral(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string',
            'company_email' => 'nullable|email',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
        ]);

        foreach ($request->only(['company_name', 'company_address', 'company_phone', 'company_email', 'timezone', 'date_format', 'currency']) as $key => $value) {
            Setting::setValue($key, $value ?? '', 'general');
        }

        if ($request->hasFile('company_logo')) {
            $request->validate(['company_logo' => 'image|max:2048']);
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::setValue('company_logo', $path, 'general');
            SettingsService::clearCache();
        }
        if ($request->hasFile('favicon')) {
            $request->validate(['favicon' => 'image|max:512']);
            $path = $request->file('favicon')->store('favicons', 'public');
            Setting::setValue('favicon', $path, 'general');
            SettingsService::clearCache();
        }

        SettingsService::clearCache();
        return back()->with('success', 'Settings saved.');
    }

    public function storeAppearance(Request $request)
    {
        $allowedFonts = array_keys(SettingsService::allowedFontFamilies());
        $request->validate([
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'accent_color' => 'nullable|string|max:20',
            'font_family' => ['nullable', 'string', Rule::in($allowedFonts)],
            'heading_font' => ['nullable', 'string', Rule::in($allowedFonts)],
            'border_radius' => 'nullable|string|max:20',
            'link_color' => 'nullable|string|max:20',
            'button_bg' => 'nullable|string|max:20',
            'sidebar_active_bg' => 'nullable|string|max:20',
            'sidebar_active_text' => 'nullable|string|max:20',
        ]);

        $keys = [
            'primary_color', 'secondary_color', 'accent_color', 'font_family', 'heading_font',
            'border_radius', 'link_color', 'button_bg', 'sidebar_active_bg', 'sidebar_active_text',
        ];
        $defaults = [
            'primary_color' => '#4f46e5',
            'secondary_color' => '#6366f1',
            'accent_color' => '#818cf8',
            'font_family' => 'system-ui',
            'heading_font' => 'system-ui',
            'border_radius' => '0.5rem',
            'link_color' => '#4f46e5',
            'button_bg' => '#4f46e5',
            'sidebar_active_bg' => 'rgba(79, 70, 229, 0.1)',
            'sidebar_active_text' => '#4f46e5',
        ];
        foreach ($keys as $key) {
            $value = $request->input($key);
            Setting::setValue($key, $value !== null && $value !== '' ? $value : $defaults[$key], 'appearance');
        }
        SettingsService::clearCache();
        return back()->with('success', 'Appearance saved.');
    }

    public function storeMail(Request $request)
    {
        $request->validate([
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        foreach (['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'] as $key) {
            Setting::setValue($key, $request->input($key), 'mail');
        }
        SettingsService::clearCache();
        return back()->with('success', 'Mail settings saved.');
    }
}
