<?php

namespace Modules\Leave\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Employee;
use Modules\Leave\Models\LeaveApprovalStep;
use Modules\Leave\Models\LeaveRequest;
use Modules\Leave\Models\LeaveType;

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
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'status' => LeaveRequest::STATUS_PENDING,
            'reason' => $request->reason,
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

        $approvers = \Modules\Core\Models\User::whereIn('role', [\Modules\Core\Models\User::ROLE_HR, \Modules\Core\Models\User::ROLE_ADMIN])->get();
        foreach ($approvers as $user) {
            $user->notify(new \Modules\Core\Notifications\LeavePendingApprovalNotification($leaveRequest));
        }

        return redirect()->route('leave.index')->with('success', 'Leave request submitted.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee', 'leaveType', 'approvedByUser', 'approvalSteps.approvedByUser']);
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
            }
        } else {
            $leaveRequest->update([
                'status' => LeaveRequest::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);
        }
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
        return back()->with('success', 'Leave request rejected.');
    }

    public function downloadLetter(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveRequest::STATUS_APPROVED) {
            return back()->with('error', 'Only approved leave requests have a letter.');
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
        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            $logoUrl = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($logoPath));
        }
        $html = view('leave::letter', [
            'leaveRequest' => $leaveRequest,
            'verificationUrl' => $verificationUrl,
            'qrImageUrl' => $qrImageUrl,
            'companyName' => $companyName,
            'logoUrl' => $logoUrl,
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
}
