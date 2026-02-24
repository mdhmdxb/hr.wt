<?php

namespace Modules\Attendance\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Attendance\Models\Attendance;
use Modules\Core\Models\Employee;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->latest('date')->latest('id')->paginate(20)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('attendance::index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        return view('attendance::create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|before_or_equal:today',
            'check_in_at' => 'nullable|date_format:H:i',
            'check_out_at' => 'nullable|date_format:H:i|after_or_equal:check_in_at',
            'status' => 'required|in:' . implode(',', Attendance::validStatuses()),
            'notes' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|max:5120',
            'overtime_minutes' => 'nullable|integer|min:0',
        ]);
        if ($request->date === Carbon::today()->format('Y-m-d') && $request->check_out_at) {
            $maxOut = now()->format('H:i');
            if ($request->check_out_at > $maxOut) {
                return back()->withInput()->withErrors(['check_out_at' => 'Check-out time cannot be in the future.']);
            }
        }

        $exists = Attendance::where('employee_id', $request->employee_id)->whereDate('date', $request->date)->first();
        if ($exists) {
            return back()->withInput()->with('error', 'Attendance already recorded for this employee on this date.');
        }

        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('attendance-attachments', 'public')
            : null;

        Attendance::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in_at' => $request->check_in_at ?: null,
            'check_out_at' => $request->check_out_at ?: null,
            'status' => $request->status,
            'notes' => $request->notes,
            'attachment_path' => $path,
            'overtime_minutes' => $request->filled('overtime_minutes') ? (int) $request->overtime_minutes : null,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded.');
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load('employee');
        if ($attendance->isLocked() && ! auth()->user()->isAdmin() && ! auth()->user()->isHr()) {
            return redirect()->route('attendance.index')->with('error', 'This record is locked. Only an administrator or HR can unlock it for editing.');
        }
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        return view('attendance::edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        if ($attendance->isLocked() && ! auth()->user()->isAdmin() && ! auth()->user()->isHr()) {
            return redirect()->route('attendance.index')->with('error', 'This record is locked.');
        }
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|before_or_equal:today',
            'check_in_at' => 'nullable|date_format:H:i',
            'check_out_at' => 'nullable|date_format:H:i',
            'status' => 'required|in:' . implode(',', Attendance::validStatuses()),
            'notes' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|max:5120',
            'overtime_minutes' => 'nullable|integer|min:0',
        ]);
        if ($request->date === Carbon::today()->format('Y-m-d') && $request->check_out_at) {
            $maxOut = now()->format('H:i');
            if ($request->check_out_at > $maxOut) {
                return back()->withInput()->withErrors(['check_out_at' => 'Check-out time cannot be in the future.']);
            }
        }

        $other = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('date', $request->date)
            ->where('id', '!=', $attendance->id)
            ->first();
        if ($other) {
            return back()->withInput()->with('error', 'Attendance already recorded for this employee on this date.');
        }

        $path = $attendance->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('attachment')->store('attendance-attachments', 'public');
        }

        $attendance->update([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in_at' => $request->check_in_at ?: null,
            'check_out_at' => $request->check_out_at ?: null,
            'status' => $request->status,
            'notes' => $request->notes,
            'attachment_path' => $path,
            'overtime_minutes' => $request->filled('overtime_minutes') ? (int) $request->overtime_minutes : null,
            'locked_at' => (auth()->user()->isAdmin() || auth()->user()->isHr()) ? now() : $attendance->locked_at,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated.');
    }

    public function unlock(Attendance $attendance)
    {
        if (! auth()->user()->isAdmin() && ! auth()->user()->isHr()) {
            return back()->with('error', 'Only an administrator or HR can unlock.');
        }
        $attendance->update(['locked_at' => null]);
        return redirect()->route('attendance.edit', $attendance)->with('success', 'Record unlocked. You can now edit. It will lock again when you save.');
    }

    public function destroy(Attendance $attendance)
    {
        if ($attendance->attachment_path && Storage::disk('public')->exists($attendance->attachment_path)) {
            Storage::disk('public')->delete($attendance->attachment_path);
        }
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted.');
    }

    public function downloadAttachment(Attendance $attendance)
    {
        if (! $attendance->attachment_path || ! Storage::disk('public')->exists($attendance->attachment_path)) {
            return back()->with('error', 'File not found.');
        }
        return Storage::disk('public')->download(
            $attendance->attachment_path,
            'attendance-' . $attendance->employee->employee_code . '-' . $attendance->date->format('Y-m-d') . '.' . pathinfo($attendance->attachment_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Batch entry: select employee + month/year, then edit all days at once.
     */
    public function batch(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $employeeId = $request->query('employee_id');
        $month = (int) $request->query('month', $request->query('m', Carbon::now()->month));
        $year = (int) $request->query('year', $request->query('y', Carbon::now()->year));

        $month = max(1, min(12, $month));
        $year = max(2020, min(2100, $year));

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;
        $existing = [];
        $employee = null;

        if ($employeeId) {
            $employee = Employee::find($employeeId);
            if ($employee) {
                $existing = Attendance::where('employee_id', $employeeId)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get()
                    ->keyBy(fn ($a) => $a->date->format('Y-m-d'));
            }
        }

        return view('attendance::batch', [
            'employees' => $employees,
            'employee' => $employee,
            'employeeId' => $employeeId,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'existing' => $existing,
        ]);
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2020,2100',
            'attendance' => 'required|array',
            'attendance.*.date' => 'required|date',
            'attendance.*.check_in_at' => 'nullable|date_format:H:i',
            'attendance.*.check_out_at' => 'nullable|date_format:H:i',
            'attendance.*.status' => 'required|in:' . implode(',', Attendance::validStatuses()),
            'attendance.*.notes' => 'nullable|string|max:500',
        ]);

        $employeeId = (int) $request->employee_id;
        $month = (int) $request->month;
        $year = (int) $request->year;

        foreach ($request->attendance as $row) {
            $date = $row['date'] ?? null;
            if (!$date) {
                continue;
            }
            $d = Carbon::parse($date);
            if ($d->month != $month || $d->year != $year) {
                continue;
            }

            $att = Attendance::firstOrNew([
                'employee_id' => $employeeId,
                'date' => $date,
            ]);
            $att->check_in_at = !empty($row['check_in_at']) ? $row['check_in_at'] : null;
            $att->check_out_at = !empty($row['check_out_at']) ? $row['check_out_at'] : null;
            $att->status = $row['status'] ?? 'present';
            $att->notes = $row['notes'] ?? null;
            $att->save();
        }

        return redirect()->route('attendance.batch', ['employee_id' => $employeeId, 'month' => $month, 'year' => $year])
            ->with('success', 'Attendance batch saved.');
    }

    /** Employee self check-in (today only, no advance). */
    public function selfCheckIn(Request $request)
    {
        $user = auth()->user();
        if (! $user->employee_id) {
            return back()->with('error', 'Your account is not linked to an employee.');
        }
        $today = Carbon::today();
        $existing = Attendance::where('employee_id', $user->employee_id)->whereDate('date', $today)->first();
        if ($existing) {
            return back()->with('error', 'You have already checked in today.');
        }
        Attendance::create([
            'employee_id' => $user->employee_id,
            'date' => $today,
            'check_in_at' => now()->format('H:i'),
            'status' => Attendance::STATUS_PRESENT,
            'locked_at' => now(),
        ]);
        return back()->with('success', 'Checked in at ' . now()->format('h:i A') . '.');
    }

    /** Employee self check-out (today only; must have checked in). */
    public function selfCheckOut(Request $request)
    {
        $user = auth()->user();
        if (! $user->employee_id) {
            return back()->with('error', 'Your account is not linked to an employee.');
        }
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $user->employee_id)->whereDate('date', $today)->first();
        if (! $attendance) {
            return back()->with('error', 'You have not checked in today.');
        }
        if ($attendance->check_out_at) {
            return back()->with('error', 'You have already checked out today.');
        }
        $attendance->update(['check_out_at' => now()->format('H:i'), 'locked_at' => now()]);
        return back()->with('success', 'Checked out at ' . now()->format('h:i A') . '.');
    }
}
