<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Core\Models\AssetType;

class AssetTypeController extends Controller
{
    public function index()
    {
        $types = AssetType::withCount('assets')->orderBy('name')->paginate(20);
        return view('core::assets.types-index', compact('types'));
    }

    public function create()
    {
        return view('core::assets.types-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        $slug = Str::slug($request->name);
        if (AssetType::where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors(['name' => 'A type with this name already exists.']);
        }
        AssetType::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description ?: null,
        ]);
        return redirect()->route('assets.types.index')->with('success', 'Asset type created.');
    }

    public function edit(AssetType $type)
    {
        return view('core::assets.types-edit', compact('type'));
    }

    public function update(Request $request, AssetType $type)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        $slug = Str::slug($request->name);
        if (AssetType::where('slug', $slug)->where('id', '!=', $type->id)->exists()) {
            return back()->withInput()->withErrors(['name' => 'A type with this name already exists.']);
        }
        $type->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description ?: null,
        ]);
        return redirect()->route('assets.types.index')->with('success', 'Asset type updated.');
    }

    public function destroy(AssetType $type)
    {
        if ($type->assets()->exists()) {
            return back()->with('error', 'Cannot delete type that has assets. Reassign or remove assets first.');
        }
        $type->delete();
        return redirect()->route('assets.types.index')->with('success', 'Asset type deleted.');
    }
}
