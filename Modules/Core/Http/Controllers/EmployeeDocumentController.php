<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Employee;
use Modules\Core\Models\EmployeeDocument;

class EmployeeDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeDocument::with(['employee', 'uploadedByUser']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->boolean('expiring')) {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '>=', now())
                ->where('expiry_date', '<=', now()->addDays(30));
        }

        $documents = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('core::documents.index', compact('documents', 'employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $selectedEmployee = $request->filled('employee_id') ? Employee::find($request->employee_id) : null;
        $renewalOf = $request->filled('renewal_of_id') ? EmployeeDocument::with('employee')->find($request->renewal_of_id) : null;
        return view('core::documents.create', compact('employees', 'selectedEmployee', 'renewalOf'));
    }

    public function store(Request $request)
    {
        $maxSetting = \Modules\Settings\Models\Setting::getValue('upload_max_employee_document_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0 ? (int) $maxSetting[0] : 10240);
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'renewal_of_id' => 'nullable|exists:employee_documents,id',
            'type' => 'required|string|in:' . implode(',', array_keys(EmployeeDocument::typeOptions())),
            'title' => 'nullable|string|max:191',
            'file' => 'required|file|max:' . $maxKb,
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('file')->store('employee-documents', 'public');

        EmployeeDocument::create([
            'employee_id' => $request->employee_id,
            'renewal_of_id' => $request->renewal_of_id ?: null,
            'type' => $request->type,
            'title' => $request->title ?: null,
            'file_path' => $path,
            'issue_date' => $request->issue_date ?: null,
            'expiry_date' => $request->expiry_date ?: null,
            'version' => $request->version ?: null,
            'notes' => $request->notes ?: null,
            'status' => EmployeeDocument::STATUS_APPROVED,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded.');
    }

    /**
     * Employee self-service: list own documents.
     */
    public function myIndex(Request $request)
    {
        $user = $request->user();
        if (! $user->employee_id) {
            abort(403);
        }
        $documents = EmployeeDocument::where('employee_id', $user->employee_id)
            ->orderByDesc('uploaded_at')
            ->get();
        return view('core::documents.my-index', compact('documents'));
    }

    /**
     * Employee self-service: show upload form (only allowed types).
     */
    public function myCreate(Request $request)
    {
        $user = $request->user();
        if (! $user->employee_id) {
            abort(403);
        }
        $employeeId = $user->employee_id;
        $types = EmployeeDocument::typeOptions();
        $today = now()->toDateString();
        $lockedTypes = [];
        foreach (array_keys($types) as $type) {
            $existing = EmployeeDocument::where('employee_id', $employeeId)
                ->where('type', $type)
                ->orderByDesc('uploaded_at')
                ->first();
            $locked = false;
            if ($existing) {
                $notExpired = $existing->expiry_date ? $existing->expiry_date->toDateString() >= $today : true;
                $locked = $notExpired && ! $existing->employee_can_upload_again;
            }
            $lockedTypes[$type] = $locked;
        }
        return view('core::documents.my-create', [
            'types' => $types,
            'lockedTypes' => $lockedTypes,
        ]);
    }

    /**
     * Employee self-service: upload own document (one active per type, unless HR unlocks or expired).
     */
    public function myStore(Request $request)
    {
        $user = $request->user();
        if (! $user->employee_id) {
            abort(403);
        }
        $maxSetting = \Modules\Settings\Models\Setting::getValue('upload_max_employee_document_kb');
        $maxKb = (is_array($maxSetting) && isset($maxSetting[0]) && (int) $maxSetting[0] > 0 ? (int) $maxSetting[0] : 10240);
        $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(EmployeeDocument::typeOptions())),
            'title' => 'nullable|string|max:191',
            'file' => 'required|file|max:' . $maxKb,
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $employeeId = $user->employee_id;
        $today = now()->toDateString();
        $existing = EmployeeDocument::where('employee_id', $employeeId)
            ->where('type', $request->type)
            ->orderByDesc('uploaded_at')
            ->first();
        if ($existing) {
            $notExpired = $existing->expiry_date ? $existing->expiry_date->toDateString() >= $today : true;
            if ($notExpired && ! $existing->employee_can_upload_again) {
                return back()->withInput()->with('error', 'You have already uploaded this type of document. Please contact HR if you need to replace it.');
            }
        }

        $path = $request->file('file')->store('employee-documents', 'public');

        EmployeeDocument::create([
            'employee_id' => $employeeId,
            'renewal_of_id' => $existing ? $existing->id : null,
            'type' => $request->type,
            'title' => $request->title ?: null,
            'file_path' => $path,
            'issue_date' => $request->issue_date ?: null,
            'expiry_date' => $request->expiry_date ?: null,
            'version' => null,
            'notes' => $request->notes ?: null,
            'status' => EmployeeDocument::STATUS_PENDING,
            'uploaded_by' => $user->id,
            'uploaded_at' => now(),
        ]);

        if ($existing && $existing->employee_can_upload_again) {
            $existing->update(['employee_can_upload_again' => false]);
        }

        return redirect()->route('my-documents.index')->with('success', 'Document uploaded and pending HR review.');
    }

    public function show(EmployeeDocument $document)
    {
        $document->load(['employee', 'uploadedByUser', 'renewalOf', 'renewals']);
        return view('core::documents.show', compact('document'));
    }

    public function destroy(EmployeeDocument $document)
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function download(EmployeeDocument $document)
    {
        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }
        return Storage::disk('public')->download(
            $document->file_path,
            ($document->title ?: $document->type) . '-' . $document->employee->employee_code . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Employee self-service: download own document.
     */
    public function myDownload(Request $request, EmployeeDocument $document)
    {
        $user = $request->user();
        if (! $user->employee_id || $document->employee_id !== $user->employee_id) {
            abort(403);
        }
        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }
        return Storage::disk('public')->download(
            $document->file_path,
            ($document->title ?: $document->type) . '-' . $document->employee->employee_code . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }
}
