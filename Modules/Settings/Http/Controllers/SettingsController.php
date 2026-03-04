<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Modules\Core\Models\Setting as CoreSetting;
use Modules\Core\Models\User;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\SettingsService;

class SettingsController extends Controller
{
    /** Keys that can be toggled for payslip display. Default all shown. */
    public static function payslipDisplayKeys(): array
    {
        return [
            'employee' => 'Employee name',
            'period' => 'Period',
            'basic' => 'Basic salary',
            'accommodation' => 'Accommodation',
            'transportation' => 'Transportation',
            'food_allowance' => 'Food allowance',
            'other_allowances' => 'Other allowances',
            'bonus' => 'Bonus',
            'days_worked' => 'Days worked',
            'days_off' => 'Days off',
            'holiday' => 'Holiday',
            'annual_leave' => 'Annual leave',
            'unpaid_leave' => 'Unpaid leave',
            'overtime_hours' => 'Overtime hours',
            'off_day_hours' => 'Hours worked on off days',
            'off_day_details' => 'Off days worked (details)',
            'overtime_premium' => 'Overtime premium (MOHRE)',
            'overtime_bonus' => 'Overtime bonus + transport + food',
            'salary_adjustment' => 'Salary adjustment (deduction)',
            'totals' => 'Totals (allowances, deductions, net, WPS)',
            'remarks' => 'Remarks',
            'notes' => 'Notes',
            'qr_verify' => 'QR / verification link',
        ];
    }

    /** Document types that can show company stamp and/or signatory signature. */
    public static function documentTypes(): array
    {
        return [
            'leave_letter' => 'Leave approval / cancellation letter',
            'payslip' => 'Payslip',
        ];
    }

    /** Default footer text for all generated letters/documents. */
    public static function defaultLetterFooterText(): string
    {
        return 'This document was generated electronically by Wise HRM and constitutes an electronic copy. For verification, use the QR code or verification link above.';
    }

    /** Placeholders available in leave/payslip templates. */
    public static function letterPlaceholders(): array
    {
        return [
            '{{ company_name }}' => 'Company name',
            '{{ employee_name }}' => 'Employee full name',
            '{{ employee_code }}' => 'Employee code',
            '{{ leave_type }}' => 'Leave type name',
            '{{ start_date }}' => 'Leave start date (e.g. 2026-03-01)',
            '{{ end_date }}' => 'Leave end date',
            '{{ total_days }}' => 'Number of leave days',
            '{{ application_date }}' => 'Date application was submitted',
            '{{ today }}' => 'Today\'s date',
        ];
    }

    public static function defaultLeaveApprovalTemplate(): string
    {
        return "Dear {{ employee_name }},\n\n"
            . "Your {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been approved."
            . "\n\nRegards,\n{{ company_name }} HR";
    }

    public static function defaultLeaveCancellationTemplate(): string
    {
        return "Dear {{ employee_name }},\n\n"
            . "Your previously approved {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been cancelled."
            . "\n\nRegards,\n{{ company_name }} HR";
    }

    /** Simple placeholder renderer for templates. */
    public static function renderTemplate(string $template, array $vars): string
    {
        return nl2br(e(strtr($template, $vars)));
    }

    public function general()
    {
        $payslipDisplayRaw = Setting::getValue('payslip_display', null);
        $decoded = is_string($payslipDisplayRaw) ? json_decode($payslipDisplayRaw, true) : null;
        $payslipDisplay = is_array($decoded) ? $decoded : array_keys(self::payslipDisplayKeys());
        $companyStampPath = Setting::getValue('company_stamp_path');
        $sigPath = auth()->user()?->signature_path;
        $signatureUrl = ($sigPath && Storage::disk('public')->exists($sigPath)) ? Storage::disk('public')->url($sigPath) : null;
        $companyStampUrl = ($companyStampPath && Storage::disk('public')->exists($companyStampPath)) ? Storage::disk('public')->url($companyStampPath) : null;
        $stampRaw = Setting::getValue('document_stamp_on', null);
        $signatureRaw = Setting::getValue('document_signature_on', null);
        $documentStampOn = is_string($stampRaw) ? (json_decode($stampRaw, true) ?: []) : [];
        $documentSignatureOn = is_string($signatureRaw) ? (json_decode($signatureRaw, true) ?: []) : [];
        $letterFooterText = Setting::getValue('letter_footer_text') ?: self::defaultLetterFooterText();
        $acceptVal = Setting::getValue('overtime_accept_partial');
        $overtimeAcceptPartial = is_array($acceptVal) && isset($acceptVal[0]) && (bool) $acceptVal[0];
        $thresholdVal = Setting::getValue('overtime_partial_threshold');
        $overtimePartialThreshold = 0;
        if (is_array($thresholdVal) && isset($thresholdVal[0])) {
            $overtimePartialThreshold = (int) $thresholdVal[0];
        }
        $leaveApprovalTemplate = Setting::getValue('template_leave_approval');
        if (! is_string($leaveApprovalTemplate) || trim($leaveApprovalTemplate) === '') {
            $leaveApprovalTemplate = self::defaultLeaveApprovalTemplate();
        }
        $leaveCancellationTemplate = Setting::getValue('template_leave_cancellation');
        if (! is_string($leaveCancellationTemplate) || trim($leaveCancellationTemplate) === '') {
            $leaveCancellationTemplate = self::defaultLeaveCancellationTemplate();
        }
        return view('settings::general', [
            'company_name' => SettingsService::get('company_name', ''),
            'company_address' => SettingsService::get('company_address', ''),
            'company_phone' => SettingsService::get('company_phone', ''),
            'company_email' => SettingsService::get('company_email', ''),
            'company_country' => SettingsService::get('company_country', ''),
            'timezone' => SettingsService::get('timezone', config('app.timezone')),
            'date_format' => SettingsService::get('date_format', 'Y-m-d'),
            'currency' => SettingsService::get('currency', 'USD'),
            'payslipDisplay' => $payslipDisplay,
            'payslipDisplayKeys' => self::payslipDisplayKeys(),
            'companyStampPath' => $companyStampPath,
            'companyStampUrl' => $companyStampUrl,
            'currentUserSignaturePath' => $sigPath,
            'signatureUrl' => $signatureUrl,
            'documentTypes' => self::documentTypes(),
            'documentStampOn' => $documentStampOn,
            'documentSignatureOn' => $documentSignatureOn,
            'letterFooterText' => $letterFooterText,
            'overtimeAcceptPartial' => $overtimeAcceptPartial,
            'overtimePartialThreshold' => $overtimePartialThreshold,
            'leaveApprovalTemplate' => $leaveApprovalTemplate,
            'leaveCancellationTemplate' => $leaveCancellationTemplate,
            'letterPlaceholders' => self::letterPlaceholders(),
            'workingScheduleOverrides' => \Modules\Core\Models\WorkingScheduleOverride::with(['branch', 'site', 'project', 'employee'])->orderBy('start_date')->get(),
            'branches' => \Modules\Core\Models\Branch::orderBy('name')->get(),
            'sites' => \Modules\Core\Models\Site::with('branch')->orderBy('name')->get(),
            'projects' => \Modules\Core\Models\Project::orderBy('name')->get(),
            'employees' => \Modules\Core\Models\Employee::where('status', 'active')->orderBy('first_name')->get(),
            'dashboardCardOrder' => self::dashboardCardOrderForSettings(),
            'dashboardCardLabels' => self::dashboardCardLabels(),
            'calendarApiUrl' => SettingsService::get('calendar_api_url', ''),
            'calendarApiKey' => SettingsService::get('calendar_api_key', ''),
        ]);
    }

    /** Dashboard card keys and labels for Settings UI. */
    public static function dashboardCardLabels(): array
    {
        return [
            'assets_expiring_soon' => 'Assets expiring soon',
            'assets_expired' => 'Assets already expired',
            'documents_expiring_soon' => 'Documents expiring soon',
            'documents_expired' => 'Documents already expired',
            'upcoming_vacations' => 'Upcoming vacations',
            'birthdays' => 'Birthdays',
            'calendar' => 'Calendar',
            'public_holidays_month' => 'Public holidays this month',
            'upcoming_public_holidays' => 'Upcoming public holidays',
            'quick_actions' => 'Quick actions',
        ];
    }

    /** Ordered list of dashboard card keys from Core setting (for Settings page). */
    public static function dashboardCardOrderForSettings(): array
    {
        $raw = CoreSetting::getValue('dashboard_cards');
        if (is_array($raw) && ! empty($raw)) {
            return $raw;
        }
        return array_keys(self::dashboardCardLabels());
    }

    /** Save dashboard card visibility and order (stored in Core settings). */
    public function storeDashboardCards(Request $request)
    {
        $labels = self::dashboardCardLabels();
        $visible = $request->input('visible', []);
        $order = $request->input('order', []);
        if (! is_array($visible)) {
            $visible = [];
        }
        if (! is_array($order)) {
            $order = [];
        }
        $ordered = [];
        foreach (array_keys($labels) as $key) {
            if (! empty($visible[$key])) {
                $ordered[] = ['key' => $key, 'order' => (int) ($order[$key] ?? 999)];
            }
        }
        usort($ordered, fn ($a, $b) => $a['order'] <=> $b['order']);
        $cards = array_column($ordered, 'key');
        if (empty($cards)) {
            $cards = array_keys($labels);
        }
        CoreSetting::setValue('dashboard_cards', $cards, null);
        \Illuminate\Support\Facades\Cache::forget('setting.global.dashboard_cards');
        return back()->with('success', 'Dashboard layout saved.');
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
            'calendar_api_url' => 'nullable|string|max:255',
            'calendar_api_key' => 'nullable|string|max:255',
        ]);

        $generalKeys = [
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'company_country',
            'timezone',
            'date_format',
            'currency',
            'calendar_api_url',
            'calendar_api_key',
        ];
        foreach ($request->only($generalKeys) as $key => $value) {
            Setting::setValue($key, $value ?? '', 'general');
        }
        // Optional letter templates from this form
        if ($request->has('template_leave_approval')) {
            Setting::setValue('template_leave_approval', $request->input('template_leave_approval'), null);
        }
        if ($request->has('template_leave_cancellation')) {
            Setting::setValue('template_leave_cancellation', $request->input('template_leave_cancellation'), null);
        }

        if ($request->hasFile('company_logo')) {
            $maxKb = $this->maxUploadKb('upload_max_logo_kb', 2048);
            $request->validate(['company_logo' => 'image|max:' . $maxKb]);
            $path = $request->file('company_logo')->store('logos', 'public');
            Setting::setValue('company_logo', $path, 'general');
            SettingsService::clearCache();
        }
        if ($request->hasFile('favicon')) {
            $maxKb = $this->maxUploadKb('upload_max_favicon_kb', 512);
            $request->validate(['favicon' => 'image|max:' . $maxKb]);
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

    public function sendTestMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        // Apply current mail settings to runtime config
        $host = $this->simpleSetting('mail_host');
        $port = (int) ($this->simpleSetting('mail_port') ?: 587);
        $user = $this->simpleSetting('mail_username');
        $pass = $this->simpleSetting('mail_password');
        $enc = $this->simpleSetting('mail_encryption') ?: null;
        $fromAddr = $this->simpleSetting('mail_from_address') ?: $user;
        $fromName = $this->simpleSetting('mail_from_name') ?: ($this->simpleSetting('company_name') ?: config('app.name'));

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.username' => $user,
            'mail.mailers.smtp.password' => $pass,
            'mail.mailers.smtp.encryption' => $enc === 'null' ? null : $enc,
            'mail.from.address' => $fromAddr,
            'mail.from.name' => $fromName,
        ]);

        try {
            Mail::raw('This is a test email from Wise HRM. If you can read this, your email settings are working.', function ($m) use ($request, $fromAddr, $fromName) {
                $m->to($request->test_email)
                    ->subject('Wise HRM – Test email');
                if ($fromAddr) {
                    $m->from($fromAddr, $fromName);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Test email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Test email sent to ' . $request->test_email . '. Please check that inbox.');
    }

    /** Save IMAP (incoming mail) settings for reading emails. */
    public function storeImap(Request $request)
    {
        $request->validate([
            'imap_host' => 'nullable|string|max:255',
            'imap_port' => 'nullable|string|max:10',
            'imap_username' => 'nullable|string|max:255',
            'imap_password' => 'nullable|string|max:255',
            'imap_encryption' => 'nullable|string|in:tls,ssl,null',
        ]);
        foreach (['imap_host', 'imap_port', 'imap_username', 'imap_password', 'imap_encryption'] as $key) {
            Setting::setValue($key, $request->input($key), 'mail');
        }
        SettingsService::clearCache();
        return back()->with('success', 'IMAP settings saved.');
    }

    public function storePayslipDisplay(Request $request)
    {
        $allowed = array_keys(self::payslipDisplayKeys());
        $selected = $request->input('payslip_display', []);
        if (! is_array($selected)) {
            $selected = [];
        }
        $selected = array_values(array_intersect($selected, $allowed));
        Setting::setValue('payslip_display', json_encode($selected), 'payslip');
        SettingsService::clearCache();
        return back()->with('success', 'Payslip display settings saved.');
    }

    public function storeOvertimeSettings(Request $request)
    {
        $request->validate([
            'overtime_accept_partial' => 'nullable|in:0,1',
            'overtime_partial_threshold' => 'nullable|integer|min:0|max:59',
        ]);
        $accept = (bool) $request->input('overtime_accept_partial', 0);
        $threshold = $request->filled('overtime_partial_threshold') ? (int) $request->overtime_partial_threshold : 0;
        Setting::setValue('overtime_accept_partial', $accept ? [1] : [0], null);
        Setting::setValue('overtime_partial_threshold', [$threshold], null);
        SettingsService::clearCache();
        return back()->with('success', 'Overtime settings saved.');
    }

    /** Management/Admin: upload my signature (used for approvals, letters). */
    public function storeSignature(Request $request)
    {
        $maxKb = $this->maxUploadKb('upload_max_signature_kb', 2048);
        $request->validate(['signature' => 'nullable|image|max:' . $maxKb]);
        if (! $request->hasFile('signature')) {
            return back()->with('error', 'Please select an image to upload.');
        }
        $user = auth()->user();
        if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
            Storage::disk('public')->delete($user->signature_path);
        }
        $path = $request->file('signature')->store('signatures', 'public');
        $user->update(['signature_path' => $path]);
        return back()->with('success', 'Your signature has been saved.');
    }

    /** HR/Admin: upload company stamp (used on letters/documents). */
    public function storeCompanyStamp(Request $request)
    {
        $maxKb = $this->maxUploadKb('upload_max_company_stamp_kb', 2048);
        $request->validate(['company_stamp' => 'nullable|image|max:' . $maxKb]);
        if (! $request->hasFile('company_stamp')) {
            return back()->with('error', 'Please select an image to upload.');
        }
        $existing = Setting::getValue('company_stamp_path', null);
        if ($existing && Storage::disk('public')->exists($existing)) {
            Storage::disk('public')->delete($existing);
        }
        $path = $request->file('company_stamp')->store('company-stamp', 'public');
        Setting::setValue('company_stamp_path', $path, 'general');
        SettingsService::clearCache();
        return back()->with('success', 'Company stamp has been saved.');
    }

    /** Save which documents show stamp/signature and the letter footer text. */
    public function storeDocumentDisplay(Request $request)
    {
        $validTypes = array_keys(self::documentTypes());
        $stamp = $request->input('document_stamp_on', []);
        $signature = $request->input('document_signature_on', []);
        if (! is_array($stamp)) {
            $stamp = [];
        }
        if (! is_array($signature)) {
            $signature = [];
        }
        $stamp = array_values(array_intersect($stamp, $validTypes));
        $signature = array_values(array_intersect($signature, $validTypes));
        Setting::setValue('document_stamp_on', json_encode($stamp), 'documents');
        Setting::setValue('document_signature_on', json_encode($signature), 'documents');
        $footer = $request->input('letter_footer_text', '');
        if (is_string($footer) && trim($footer) !== '') {
            Setting::setValue('letter_footer_text', trim($footer), 'documents');
        } else {
            Setting::setValue('letter_footer_text', self::defaultLetterFooterText(), 'documents');
        }
        return back()->with('success', 'Letter and document display settings saved.');
    }

    /** Add a working schedule override (e.g. Ramadan: reduced hours). Scope: global or per branch/site/project/employee. */
    public function storeWorkingScheduleOverride(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'branch_id' => 'nullable|exists:branches,id',
            'site_id' => 'nullable|exists:sites,id',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);
        $data = $request->only(['name', 'start_date', 'end_date', 'work_start', 'work_end', 'branch_id', 'site_id', 'project_id', 'employee_id']);
        $data['branch_id'] = $data['site_id'] = $data['project_id'] = $data['employee_id'] = null;
        if ($request->filled('employee_id')) {
            $data['employee_id'] = $request->employee_id;
        } elseif ($request->filled('project_id')) {
            $data['project_id'] = $request->project_id;
        } elseif ($request->filled('site_id')) {
            $data['site_id'] = $request->site_id;
        } elseif ($request->filled('branch_id')) {
            $data['branch_id'] = $request->branch_id;
        }
        \Modules\Core\Models\WorkingScheduleOverride::create($data);
        SettingsService::clearCache();
        return back()->with('success', 'Working schedule override added.');
    }

    /** Remove a working schedule override. */
    public function destroyWorkingScheduleOverride(\Modules\Core\Models\WorkingScheduleOverride $override)
    {
        $override->delete();
        SettingsService::clearCache();
        return back()->with('success', 'Override removed.');
    }

    /**
     * Helper: get max upload size in KB from settings, with default.
     */
    private function maxUploadKb(string $key, int $defaultKb): int
    {
        $val = Setting::getValue($key);
        if (is_array($val) && isset($val[0]) && (int) $val[0] > 0) {
            return (int) $val[0];
        }
        return $defaultKb;
    }

    /**
     * Helper: unwrap simple scalar (first element) from settings.
     */
    private function simpleSetting(string $key): ?string
    {
        $v = Setting::getValue($key);
        if (is_array($v)) {
            return isset($v[0]) ? (string) $v[0] : null;
        }
        return $v !== null ? (string) $v : null;
    }
}
