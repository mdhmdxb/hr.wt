<?php

namespace Modules\Leave\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Leave\Models\LeaveRequest;

class LeaveVerificationController extends Controller
{
    /** Public route: verify a leave request via token (e.g. from QR on letter). */
    public function show(Request $request, LeaveRequest $leaveRequest)
    {
        $token = $request->query('token');
        $valid = $token && hash_equals((string) $leaveRequest->verification_token, $token);

        $leaveRequest->load(['employee', 'leaveType', 'approvalSteps.approvedByUser']);

        return view('leave::verify-leave', [
            'leaveRequest' => $leaveRequest,
            'valid' => $valid,
        ]);
    }
}
