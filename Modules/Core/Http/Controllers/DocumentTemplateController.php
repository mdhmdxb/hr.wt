<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\DocumentTemplate;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $templates = DocumentTemplate::orderBy('name')->paginate(20);
        return view('core::templates.index', compact('templates'));
    }

    public function create()
    {
        return view('core::templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:document_templates,slug',
            'content' => 'nullable|string|max:50000',
            'variables_help' => 'nullable|string|max:1000',
        ]);
        DocumentTemplate::create($request->only(['name', 'slug', 'content', 'variables_help']));
        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function edit(DocumentTemplate $template)
    {
        return view('core::templates.edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:document_templates,slug,' . $template->id,
            'content' => 'nullable|string|max:50000',
            'variables_help' => 'nullable|string|max:1000',
        ]);
        $template->update($request->only(['name', 'slug', 'content', 'variables_help']));
        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(DocumentTemplate $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }

    public function preview(Request $request, DocumentTemplate $template)
    {
        $sample = $request->get('sample', []);
        if (is_string($sample)) {
            $sample = json_decode($sample, true) ?: [];
        }
        if (empty($sample)) {
            $sample = [
                'company_name' => 'Acme Ltd',
                'employee_name' => 'John Doe',
                'date' => now()->format('Y-m-d'),
                'leave_type' => 'Annual Leave',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(3)->format('Y-m-d'),
                'days' => '4',
            ];
        }
        $html = $template->renderWith($sample);
        return view('core::templates.preview', compact('template', 'html', 'sample'));
    }
}
