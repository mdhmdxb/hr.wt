<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Setting;
use Modules\Settings\Http\Controllers\SettingsController as SettingsHelpers;
use Modules\Settings\Models\Setting as SettingsSetting;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = [
            'email_leave_approval' => [
                'label' => 'Leave approved (to employee)',
                'default' => "Dear {{ employee_name }},\n\nYour {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been approved.\n\nRegards,\n{{ company_name }} HR",
            ],
            'email_leave_rejection' => [
                'label' => 'Leave rejected (to employee)',
                'default' => "Dear {{ employee_name }},\n\nYour {{ leave_type }} from {{ start_date }} to {{ end_date }} could not be approved.\n\nRegards,\n{{ company_name }} HR",
            ],
            'email_leave_cancellation' => [
                'label' => 'Leave cancelled (to employee)',
                'default' => "Dear {{ employee_name }},\n\nYour previously approved {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been cancelled.\n\nRegards,\n{{ company_name }} HR",
            ],
        ];

        foreach ($templates as $key => &$tpl) {
            $val = Setting::getValue($key);
            $tpl['value'] = is_array($val) && isset($val[0]) && is_string($val[0]) && trim($val[0]) !== ''
                ? $val[0]
                : $tpl['default'];
        }
        unset($tpl);

        $placeholders = SettingsHelpers::letterPlaceholders();

        // Letter templates (PDF body for leave approval/cancellation letters)
        $leaveApprovalLetter = SettingsSetting::getValue('template_leave_approval');
        $leaveCancellationLetter = SettingsSetting::getValue('template_leave_cancellation');
        if (! is_string($leaveApprovalLetter) || trim($leaveApprovalLetter) === '') {
            $leaveApprovalLetter = SettingsHelpers::defaultLeaveApprovalTemplate();
        }
        if (! is_string($leaveCancellationLetter) || trim($leaveCancellationLetter) === '') {
            $leaveCancellationLetter = SettingsHelpers::defaultLeaveCancellationTemplate();
        }

        return view('core::email-templates.index', compact('templates', 'placeholders', 'leaveApprovalLetter', 'leaveCancellationLetter'));
    }

    public function update(Request $request)
    {
        $keys = ['email_leave_approval', 'email_leave_rejection', 'email_leave_cancellation'];
        foreach ($keys as $key) {
            $val = (string) $request->input($key, '');
            Setting::setValue($key, [$val], null);
        }

        if ($request->has('template_leave_approval')) {
            SettingsSetting::setValue('template_leave_approval', $request->input('template_leave_approval'), 'general');
        }
        if ($request->has('template_leave_cancellation')) {
            SettingsSetting::setValue('template_leave_cancellation', $request->input('template_leave_cancellation'), 'general');
        }

        return back()->with('success', 'Templates saved.');
    }
}

