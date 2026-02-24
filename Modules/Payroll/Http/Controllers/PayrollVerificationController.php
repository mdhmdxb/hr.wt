<?php

namespace Modules\Payroll\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payroll\Models\Payslip;

class PayrollVerificationController extends Controller
{
    /** Public route: verify a payslip via token (e.g. from QR code). */
    public function show(Request $request, Payslip $payslip)
    {
        $token = $request->query('token');
        $valid = $token && hash_equals((string) $payslip->verification_token, $token);

        $payslip->load(['payrollRun', 'employee']);

        return view('payroll::verify-payslip', [
            'payslip' => $payslip,
            'valid' => $valid,
        ]);
    }
}
