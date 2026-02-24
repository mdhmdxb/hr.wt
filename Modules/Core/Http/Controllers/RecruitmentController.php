<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobCandidate;
use Modules\Core\Models\JobOpening;

class RecruitmentController extends Controller
{
    public function index()
    {
        $openings = JobOpening::withCount('candidates')->with('department')->orderByDesc('created_at')->paginate(15);
        return view('core::recruitment.index', compact('openings'));
    }

    public function createOpening()
    {
        $departments = Department::orderBy('name')->get();
        return view('core::recruitment.openings-create', compact('departments'));
    }

    public function storeOpening(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string|max:5000',
            'requirements' => 'nullable|string|max:5000',
        ]);
        JobOpening::create([
            'title' => $request->title,
            'department_id' => $request->department_id ?: null,
            'status' => JobOpening::STATUS_OPEN,
            'description' => $request->description ?: null,
            'requirements' => $request->requirements ?: null,
        ]);
        return redirect()->route('recruitment.index')->with('success', 'Job opening created.');
    }

    public function showOpening(JobOpening $opening)
    {
        $opening->load(['department', 'candidates']);
        return view('core::recruitment.openings-show', compact('opening'));
    }

    public function editOpening(JobOpening $opening)
    {
        $departments = Department::orderBy('name')->get();
        return view('core::recruitment.openings-edit', compact('opening', 'departments'));
    }

    public function updateOpening(Request $request, JobOpening $opening)
    {
        $request->validate([
            'title' => 'required|string|max:191',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:open,closed',
            'description' => 'nullable|string|max:5000',
            'requirements' => 'nullable|string|max:5000',
        ]);
        $opening->update([
            'title' => $request->title,
            'department_id' => $request->department_id ?: null,
            'status' => $request->status,
            'closed_at' => $request->status === JobOpening::STATUS_CLOSED ? ($opening->closed_at ?? now()) : null,
            'description' => $request->description ?: null,
            'requirements' => $request->requirements ?: null,
        ]);
        return redirect()->route('recruitment.show', $opening)->with('success', 'Opening updated.');
    }

    public function storeCandidate(Request $request, JobOpening $opening)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
        JobCandidate::create([
            'job_opening_id' => $opening->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?: null,
            'stage' => JobCandidate::STAGE_APPLIED,
            'notes' => $request->notes ?: null,
        ]);
        return back()->with('success', 'Candidate added.');
    }

    public function updateCandidateStage(Request $request, JobCandidate $candidate)
    {
        $request->validate(['stage' => 'required|in:' . implode(',', array_keys(JobCandidate::stageOptions()))]);
        $candidate->update([
            'stage' => $request->stage,
            'interview_at' => $request->stage === JobCandidate::STAGE_INTERVIEW && ! $candidate->interview_at ? now() : $candidate->interview_at,
        ]);
        return back()->with('success', 'Stage updated.');
    }

    public function updateCandidate(Request $request, JobCandidate $candidate)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'interview_at' => 'nullable|date',
        ]);
        $candidate->update($request->only(['name', 'email', 'phone', 'notes', 'interview_at']));
        return back()->with('success', 'Candidate updated.');
    }
}
