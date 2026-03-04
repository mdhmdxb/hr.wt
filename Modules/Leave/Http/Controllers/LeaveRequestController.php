<?php

namespace Modules\Leave\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Models\Employee;
use Modules\Leave\Models\LeaveApprovalStep;
use Modules\Leave\Models\LeaveRequest;
use Modules\Leave\Models\LeaveType;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Models\Setting as SettingsSetting;

class LeaveRequestController extends Controller
{
    public function calendar(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $start = \Carbon\Carbon::createFromDate($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $leaveRequests = LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                    });
            })
            ->orderBy('start_date')
            ->get();
        if ($request->filled('employee_id')) {
            $leaveRequests = $leaveRequests->filter(fn ($r) => (int) $r->employee_id === (int) $request->employee_id)->values();
        }
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        return view('leave::requests.calendar', compact('year', 'month', 'start', 'leaveRequests', 'employees'));
    }

    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('leave::requests.index', compact('leaveRequests', 'employees'));
    }

    /** Employee: my leave list (only their own requests). */
    public function myIndex(Request $request)
    {
        $user = auth()->user();
        if (! $user->employee_id) {
            return redirect()->route('dashboard')->with('error', 'Your account is not linked to an employee.');
        }
        $query = LeaveRequest::with(['leaveType'])
            ->where('employee_id', $user->employee_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query->latest()->paginate(20)->withQueryString();

        return view('leave::my.index', compact('leaveRequests'));
    }

    /** Employee: create leave for self. */
    public function myCreate()
    {
        $user = auth()->user();
        if (! $user->employee_id) {
            return redirect()->route('dashboard')->with('error', 'Your account is not linked to an employee.');
        }
        $leaveTypes = LeaveType::orderBy('name')->get();
        if ($leaveTypes->isEmpty()) {
            return redirect()->route('my-leave.index')->with('error', 'Leave types are not configured yet. Contact HR.');
        }
        return view('leave::my.create', compact('leaveTypes'));
    }

    /** Employee: store leave for self. */
    public function myStore(Request $request)
    {
        $user = auth()->user();
        if (! $user->employee_id) {
            return redirect()->route('dashboard')->with('error', 'Your account is not linked to an employee.');
        }
        $maxSetting = SettingsSetting::getValue('upload_max_leave_document_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0) ? (int) $maxSetting[0] : 5120;
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'document' => 'nullable|file|max:' . $maxKb,
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        if ($leaveType->allow_document && $leaveType->require_document && ! $request->hasFile('document')) {
            return back()->withInput()->withErrors(['document' => 'Supporting document is required for this leave type.']);
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $docPath = null;
        if ($leaveType->allow_document && $request->hasFile('document')) {
            $docPath = $request->file('document')->store('leave-docs', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $user->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'status' => LeaveRequest::STATUS_PENDING,
            'reason' => $request->reason,
            'supporting_document_path' => $docPath,
        ]);

        $leaveType = $leaveRequest->leaveType;
        $steps = $leaveType->getWorkflowStepsNormalized();
        foreach ($steps as $idx => $step) {
            LeaveApprovalStep::create([
                'leave_request_id' => $leaveRequest->id,
                'step_order' => $idx + 1,
                'approver_type' => $step['approver'] ?? 'hr',
                'status' => LeaveApprovalStep::STATUS_PENDING,
            ]);
        }

        // Notify HR/Admin approvers
        $approvers = \Modules\Core\Models\User::whereIn('role', [\Modules\Core\Models\User::ROLE_HR, \Modules\Core\Models\User::ROLE_ADMIN, \Modules\Core\Models\User::ROLE_MANAGEMENT])->get();
        foreach ($approvers as $hrUser) {
            $hrUser->notify(new \Modules\Core\Notifications\LeavePendingApprovalNotification($leaveRequest));
        }

        return redirect()->route('my-leave.index')->with('success', 'Leave request submitted.');
    }

    /** Employee: view own leave request. */
    public function myShow(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        if ($user->employee_id !== $leaveRequest->employee_id) {
            abort(403);
        }
        $leaveRequest->load(['leaveType', 'approvalSteps.approvedByUser']);
        return view('leave::my.show', ['leaveRequest' => $leaveRequest]);
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $leaveTypes = LeaveType::orderBy('name')->get();
        if ($leaveTypes->isEmpty()) {
            return redirect()->route('leave.index')->with('error', 'Add at least one leave type first.');
        }
        return view('leave::requests.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $maxSetting = SettingsSetting::getValue('upload_max_leave_document_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0) ? (int) $maxSetting[0] : 5120;
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'document' => 'nullable|file|max:' . $maxKb,
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        if ($leaveType->allow_document && $leaveType->require_document && ! $request->hasFile('document')) {
            return back()->withInput()->withErrors(['document' => 'Supporting document is required for this leave type.']);
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $docPath = null;
        if ($leaveType->allow_document && $request->hasFile('document')) {
            $docPath = $request->file('document')->store('leave-docs', 'public');
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'status' => LeaveRequest::STATUS_PENDING,
            'reason' => $request->reason,
            'supporting_document_path' => $docPath,
        ]);

        $leaveType = $leaveRequest->leaveType;
        $steps = $leaveType->getWorkflowStepsNormalized();
        foreach ($steps as $idx => $step) {
            LeaveApprovalStep::create([
                'leave_request_id' => $leaveRequest->id,
                'step_order' => $idx + 1,
                'approver_type' => $step['approver'] ?? 'hr',
                'status' => LeaveApprovalStep::STATUS_PENDING,
            ]);
        }

        $approvers = \Modules\Core\Models\User::whereIn('role', [\Modules\Core\Models\User::ROLE_HR, \Modules\Core\Models\User::ROLE_ADMIN, \Modules\Core\Models\User::ROLE_MANAGEMENT])->get();
        foreach ($approvers as $user) {
            $user->notify(new \Modules\Core\Notifications\LeavePendingApprovalNotification($leaveRequest));
        }

        return redirect()->route('leave.index')->with('success', 'Leave request submitted.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee', 'leaveType', 'approvedByUser', 'cancelledByUser', 'approvalSteps.approvedByUser']);
        return view('leave::requests.show', ['leaveRequest' => $leaveRequest]);
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
            return back()->with('error', 'Request is already processed.');
        }
        $user = auth()->user();
        if (! $leaveRequest->canBeActedOnBy($user)) {
            return back()->with('error', 'You are not the current approver for this request.');
        }

        $step = $leaveRequest->currentApprovalStep();
        if ($step) {
            $step->update([
                'status' => LeaveApprovalStep::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            $next = $leaveRequest->currentApprovalStep();
            if (! $next) {
                $leaveRequest->update([
                    'status' => LeaveRequest::STATUS_APPROVED,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);
                $this->deductLeaveBalance($leaveRequest);
            }
        } else {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);
            $this->deductLeaveBalance($leaveRequest);
        }
        $this->sendLeaveEmail($leaveRequest, 'email_leave_approval');
        return back()->with('success', 'Leave request approved.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_PENDING) {
            return back()->with('error', 'Request is already processed.');
        }
        $user = auth()->user();
        if (! $leaveRequest->canBeActedOnBy($user)) {
            return back()->with('error', 'You are not the current approver for this request.');
        }
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $step = $leaveRequest->currentApprovalStep();
        if ($step) {
            $step->update([
                'status' => LeaveApprovalStep::STATUS_REJECTED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'notes' => $request->rejection_reason,
            ]);
        }
        $leaveRequest->update([
            'status' => LeaveRequest::STATUS_REJECTED,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        $this->sendLeaveEmail($leaveRequest, 'email_leave_rejection');
        return back()->with('success', 'Leave request rejected.');
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        if (! $leaveRequest->canBeCancelledBy($user)) {
            return back()->with('error', 'You are not allowed to cancel this request.');
        }
        $request->validate(['cancel_reason' => 'nullable|string|max:500']);

        $wasApproved = $leaveRequest->status === LeaveRequest::STATUS_APPROVED;
        $leaveRequest->update([
            'status' => LeaveRequest::STATUS_CANCELLED,
            'cancelled_by' => $user->id,
            'cancelled_at' => now(),
            'cancel_reason' => $request->cancel_reason,
        ]);
        if ($wasApproved) {
            $this->returnLeaveBalance($leaveRequest);
        }
        $this->sendLeaveEmail($leaveRequest, 'email_leave_cancellation');
        return back()->with('success', 'Leave request cancelled.');
    }

    /** Deduct approved leave days from employee remaining balance (only if balance is tracked). */
    protected function deductLeaveBalance(LeaveRequest $leaveRequest): void
    {
        $emp = $leaveRequest->employee;
        if (! $emp || $emp->remaining_leave === null) {
            return;
        }
        $current = (float) $emp->remaining_leave;
        $emp->update(['remaining_leave' => max(0, $current - (float) $leaveRequest->days)]);
    }

    /** Return previously deducted days to employee when an approved leave is cancelled. */
    protected function returnLeaveBalance(LeaveRequest $leaveRequest): void
    {
        $emp = $leaveRequest->employee;
        if (! $emp || $emp->remaining_leave === null) {
            return;
        }
        $current = (float) $emp->remaining_leave;
        $emp->update(['remaining_leave' => $current + (float) $leaveRequest->days]);
    }

    /** Record actual return date: early return adds days back to balance; overstay is recorded as unpaid days. */
    public function recordReturn(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_APPROVED) {
            return back()->with('error', 'Only approved leave can have a return date recorded.');
        }
        $request->validate(['actual_return_date' => 'required|date']);
        $returnDate = Carbon::parse($request->actual_return_date)->startOfDay();
        $endDate = $leaveRequest->end_date->copy()->startOfDay();
        $startDate = $leaveRequest->start_date->copy()->startOfDay();
        $overstayDays = null;
        $daysToAddBack = 0;
        if ($returnDate->gt($endDate)) {
            $overstayDays = (int) $endDate->diffInDays($returnDate);
        } elseif ($returnDate->lt($endDate)) {
            $daysToAddBack = (int) $returnDate->diffInDays($endDate);
            if ($daysToAddBack > 0) {
                $emp = $leaveRequest->employee;
                if ($emp && $emp->remaining_leave !== null) {
                    $emp->update(['remaining_leave' => (float) $emp->remaining_leave + $daysToAddBack]);
                }
            }
        }
        $leaveRequest->update([
            'actual_return_date' => $returnDate,
            'overstay_days' => $overstayDays,
        ]);
        $msg = $overstayDays
            ? "Return recorded. {$overstayDays} overstay day(s) will be treated as unpaid leave."
            : ($daysToAddBack ? "Return recorded. {$daysToAddBack} day(s) added back to remaining leave." : 'Return date recorded.');
        return back()->with('success', $msg);
    }

    /**
     * Send a leave-related email to the employee using the configured templates.
     * $templateKey: email_leave_approval | email_leave_rejection | email_leave_cancellation
     */
    protected function sendLeaveEmail(LeaveRequest $leaveRequest, string $templateKey): void
    {
        try {
            $employee = $leaveRequest->employee;
            if (! $employee || ! $employee->email) {
                return;
            }
            $templateArr = SettingsSetting::getValue($templateKey);
            $template = is_array($templateArr) && isset($templateArr[0]) && is_string($templateArr[0]) && trim($templateArr[0]) !== ''
                ? $templateArr[0]
                : null;
            if (! $template) {
                // Fallback defaults
                if ($templateKey === 'email_leave_rejection') {
                    $template = "Dear {{ employee_name }},\n\nYour leave application was declined.\n\nReason: {{ rejection_reason }}\n\nRegards,\n{{ company_name }} HR";
                } elseif ($templateKey === 'email_leave_cancellation') {
                    $template = "Dear {{ employee_name }},\n\nYour previously approved {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been cancelled.\n\nRegards,\n{{ company_name }} HR";
                } else {
                    $template = "Dear {{ employee_name }},\n\nYour {{ leave_type }} from {{ start_date }} to {{ end_date }} ({{ total_days }} day(s)) has been approved.\n\nRegards,\n{{ company_name }} HR";
                }
            }
            $companyName = \Modules\Settings\Services\SettingsService::get('company_name', '') ?: config('app.name');
            $vars = [
                '{{ company_name }}' => $companyName,
                '{{ employee_name }}' => $employee->full_name ?? '',
                '{{ employee_code }}' => $employee->employee_code ?? '',
                '{{ leave_type }}' => $leaveRequest->leaveType->name ?? '',
                '{{ start_date }}' => $leaveRequest->start_date->format('Y-m-d'),
                '{{ end_date }}' => $leaveRequest->end_date->format('Y-m-d'),
                '{{ total_days }}' => (string) $leaveRequest->days,
                '{{ application_date }}' => $leaveRequest->created_at?->format('Y-m-d') ?? '',
                '{{ today }}' => now()->format('Y-m-d'),
                '{{ rejection_reason }}' => $templateKey === 'email_leave_rejection' ? ($leaveRequest->rejection_reason ?? 'Not provided') : '',
            ];
            $body = strtr($template, $vars);

            $subject = match ($templateKey) {
                'email_leave_rejection' => 'Your leave request was rejected',
                'email_leave_cancellation' => 'Your leave request was cancelled',
                default => 'Your leave request was approved',
            };

            Mail::raw($body, function ($m) use ($employee, $subject) {
                $m->to($employee->email)
                    ->subject('Wise HRM – ' . $subject);
            });
        } catch (\Throwable $e) {
            // Fail silently – do not break approvals if mail is misconfigured
        }
    }

    public function downloadLetter(LeaveRequest $leaveRequest)
    {
        if (! in_array($leaveRequest->status, [LeaveRequest::STATUS_APPROVED, LeaveRequest::STATUS_CANCELLED], true)) {
            return back()->with('error', 'Only approved or cancelled leave requests have a letter.');
        }
        $leaveRequest->load(['employee', 'leaveType', 'approvalSteps.approvedByUser']);
        if (empty($leaveRequest->verification_token)) {
            $leaveRequest->update(['verification_token' => \Illuminate\Support\Str::random(48)]);
        }
        $verificationUrl = url()->route('leave.verify', ['leaveRequest' => $leaveRequest->id, 'token' => $leaveRequest->verification_token]);
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->margin(1)->generate($verificationUrl);
        $qrImageUrl = 'data:image/svg+xml;base64,' . base64_encode($svg);
        $companyName = \Modules\Settings\Services\SettingsService::get('company_name', '') ?: config('app.name');
        $logoUrl = null;
        $logoPath = \Modules\Settings\Services\SettingsService::get('company_logo');
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoUrl = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($logoPath));
        }
        $documentStampOn = json_decode(SettingsSetting::getValue('document_stamp_on', '[]') ?: '[]', true) ?: [];
        $documentSignatureOn = json_decode(SettingsSetting::getValue('document_signature_on', '[]') ?: '[]', true) ?: [];
        $showStamp = in_array('leave_letter', $documentStampOn, true);
        $showSignature = in_array('leave_letter', $documentSignatureOn, true);
        $stampImageUrl = null;
        if ($showStamp) {
            $stampPath = SettingsSetting::getValue('company_stamp_path');
            if ($stampPath && Storage::disk('public')->exists($stampPath)) {
                $mime = str_ends_with(strtolower($stampPath), '.png') ? 'png' : 'jpeg';
                $stampImageUrl = 'data:image/' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($stampPath));
            }
        }
        $approvalStepSignatures = [];
        if ($showSignature && $leaveRequest->approvalSteps->isNotEmpty()) {
            foreach ($leaveRequest->approvalSteps as $step) {
                if ($step->status !== LeaveApprovalStep::STATUS_APPROVED || ! $step->approved_by) {
                    continue;
                }
                $user = $step->approvedByUser;
                if (! $user || ! $user->signature_path) {
                    continue;
                }
                $sigPath = $user->signature_path;
                if (Storage::disk('public')->exists($sigPath)) {
                    $mime = str_ends_with(strtolower($sigPath), '.png') ? 'png' : 'jpeg';
                    $approvalStepSignatures[$step->id] = 'data:image/' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($sigPath));
                }
            }
        }
        $letterFooterText = SettingsSetting::getValue('letter_footer_text');
        if (! is_string($letterFooterText) || trim($letterFooterText) === '') {
            $letterFooterText = SettingsController::defaultLetterFooterText();
        }
        // Letter body template
        $templateKey = $leaveRequest->status === LeaveRequest::STATUS_CANCELLED ? 'template_leave_cancellation' : 'template_leave_approval';
        $templateRaw = SettingsSetting::getValue($templateKey);
        if (! is_string($templateRaw) || trim($templateRaw) === '') {
            $templateRaw = $leaveRequest->status === LeaveRequest::STATUS_CANCELLED
                ? SettingsController::defaultLeaveCancellationTemplate()
                : SettingsController::defaultLeaveApprovalTemplate();
        }
        $vars = [
            '{{ company_name }}' => $companyName,
            '{{ employee_name }}' => $leaveRequest->employee->full_name ?? '',
            '{{ employee_code }}' => $leaveRequest->employee->employee_code ?? '',
            '{{ leave_type }}' => $leaveRequest->leaveType->name ?? '',
            '{{ start_date }}' => $leaveRequest->start_date->format('Y-m-d'),
            '{{ end_date }}' => $leaveRequest->end_date->format('Y-m-d'),
            '{{ total_days }}' => (string) $leaveRequest->days,
            '{{ application_date }}' => $leaveRequest->created_at?->format('Y-m-d') ?? '',
            '{{ today }}' => now()->format('Y-m-d'),
        ];
        $bodyHtml = SettingsController::renderTemplate($templateRaw, $vars);
        $html = view('leave::letter', [
            'leaveRequest' => $leaveRequest,
            'verificationUrl' => $verificationUrl,
            'qrImageUrl' => $qrImageUrl,
            'companyName' => $companyName,
            'logoUrl' => $logoUrl,
            'letterFooterText' => $letterFooterText,
            'showStamp' => $showStamp && $stampImageUrl,
            'stampImageUrl' => $stampImageUrl,
            'showSignature' => $showSignature,
            'approvalStepSignatures' => $approvalStepSignatures,
            'bodyHtml' => $bodyHtml,
        ])->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4');
        return $pdf->download('leave-letter-' . $leaveRequest->id . '.pdf');
    }

    public function leaveQr(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_APPROVED) {
            abort(404);
        }
        if (empty($leaveRequest->verification_token)) {
            $leaveRequest->update(['verification_token' => \Illuminate\Support\Str::random(48)]);
        }
        $url = url()->route('leave.verify', ['leaveRequest' => $leaveRequest->id, 'token' => $leaveRequest->verification_token]);
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->margin(2)->generate($url);
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function downloadDocument(LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        $path = $leaveRequest->supporting_document_path;
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return back()->with('error', 'No supporting document uploaded for this request.');
        }
        // Only HR/Admin/Manager/Accounts or the employee themselves can download
        $isOwner = $user->employee_id && $user->employee_id === $leaveRequest->employee_id;
        $isStaff = $user->isAdmin() || $user->isHr() || $user->isManager() || $user->isAccounts();
        if (! $isOwner && ! $isStaff) {
            abort(403);
        }
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $name = 'leave-doc-' . $leaveRequest->id . ($ext ? '.' . $ext : '');
        return Storage::disk('public')->download($path, $name);
    }
}
