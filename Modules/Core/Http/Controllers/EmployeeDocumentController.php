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
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'renewal_of_id' => 'nullable|exists:employee_documents,id',
            'type' => 'required|string|in:' . implode(',', array_keys(EmployeeDocument::typeOptions())),
            'title' => 'nullable|string|max:191',
            'file' => 'required|file|max:10240',
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
}
