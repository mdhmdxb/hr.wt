<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Employee;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $employee = $user->employee_id ? Employee::with(['branch', 'department', 'site', 'designation'])->find($user->employee_id) : null;

        return view('core::auth.profile', compact('user', 'employee'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (! $user->employee_id) {
            return redirect()->route('profile.edit')->with('success', 'Profile updated.');
        }

        $data = $request->validate([
            'phone' => 'nullable|string|max:50',
            'permanent_address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:50',
        ]);

        $employee = Employee::findOrFail($user->employee_id);
        $employee->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    public function updateSignature(Request $request)
    {
        $request->validate(['signature' => 'nullable|image|max:2048']);
        if (! $request->hasFile('signature')) {
            return back()->with('error', 'Please select an image to upload.');
        }

        $user = $request->user();
        if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
            Storage::disk('public')->delete($user->signature_path);
        }
        $path = $request->file('signature')->store('signatures', 'public');
        $user->update(['signature_path' => $path]);

        return back()->with('success', 'Your signature has been saved.');
    }
}

