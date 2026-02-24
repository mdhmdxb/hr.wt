<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Asset;
use Modules\Core\Models\AssetAssignment;
use Modules\Core\Models\AssetType;
use Modules\Core\Models\Employee;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['assetType', 'currentAssignment.employee']);

        if ($request->filled('asset_type_id')) {
            $query->where('asset_type_id', $request->asset_type_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_id')) {
            $query->whereHas('assignments', function ($q) use ($request) {
                $q->where('employee_id', $request->employee_id)->whereNull('returned_at');
            });
        }

        $assets = $query->latest()->paginate(20)->withQueryString();
        $assetTypes = AssetType::orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        $assetsExpiring = Asset::whereNotNull('expiry_date')
            ->where(function ($q) {
                $q->where('expiry_date', '<=', now()->addDays(30))->orWhere('expiry_date', '<', now());
            })
            ->orderBy('expiry_date')
            ->get();

        return view('core::assets.index', compact('assets', 'assetTypes', 'employees', 'assetsExpiring'));
    }

    public function create()
    {
        $assetTypes = AssetType::orderBy('name')->get();
        return view('core::assets.create', compact('assetTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:191',
            'identifier' => 'nullable|string|max:191',
            'status' => 'required|string|in:' . implode(',', array_keys(Asset::statusOptions())),
            'notes' => 'nullable|string|max:1000',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);

        $meta = $this->metaFromRequest($request, AssetType::find($request->asset_type_id));

        Asset::create([
            'asset_type_id' => $request->asset_type_id,
            'name' => $request->name,
            'identifier' => $request->identifier ?: null,
            'status' => $request->status,
            'notes' => $request->notes ?: null,
            'issue_date' => $request->filled('issue_date') ? $request->issue_date : null,
            'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : null,
            'meta' => $meta ?: null,
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset created.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['assetType', 'currentAssignment.employee', 'assignments.employee', 'assignments.assignedByUser']);
        return view('core::assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $assetTypes = AssetType::orderBy('name')->get();
        return view('core::assets.edit', compact('asset', 'assetTypes'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'name' => 'required|string|max:191',
            'identifier' => 'nullable|string|max:191',
            'status' => 'required|string|in:' . implode(',', array_keys(Asset::statusOptions())),
            'notes' => 'nullable|string|max:1000',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);

        $meta = $this->metaFromRequest($request, $asset->assetType);

        $asset->update([
            'asset_type_id' => $request->asset_type_id,
            'name' => $request->name,
            'identifier' => $request->identifier ?: null,
            'status' => $request->status,
            'notes' => $request->notes ?: null,
            'issue_date' => $request->filled('issue_date') ? $request->issue_date : null,
            'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : null,
            'meta' => $meta ?: null,
        ]);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated.');
    }

    public function assign(Request $request, Asset $asset)
    {
        if ($asset->currentAssignment()) {
            return back()->with('error', 'Asset is already assigned.');
        }
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'condition' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'assigned_at' => now(),
            'condition' => $request->condition ?: null,
            'notes' => $request->notes ?: null,
            'assigned_by' => auth()->id(),
        ]);
        $asset->update(['status' => Asset::STATUS_ASSIGNED]);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset assigned.');
    }

    public function return(Request $request, Asset $asset)
    {
        $assignment = $asset->currentAssignment();
        if (! $assignment) {
            return back()->with('error', 'Asset is not currently assigned.');
        }
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $assignment->update([
            'returned_at' => now(),
            'notes' => ($assignment->notes ? $assignment->notes . "\n" : '') . ($request->notes ?: 'Returned.'),
            'returned_to' => auth()->id(),
        ]);
        $asset->update(['status' => Asset::STATUS_AVAILABLE]);

        return redirect()->route('assets.show', $asset)->with('success', 'Asset returned.');
    }

    /** Build meta array from request for type-specific fields. */
    private function metaFromRequest(Request $request, ?AssetType $type): array
    {
        if (! $type) {
            return [];
        }
        $labels = Asset::metaFieldLabels($type->slug);
        $meta = [];
        foreach (array_keys($labels) as $key) {
            if ($request->filled("meta.{$key}")) {
                $meta[$key] = $request->input("meta.{$key}");
            }
        }
        return $meta;
    }
}
