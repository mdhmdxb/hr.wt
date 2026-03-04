<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\User;
use Modules\Core\Models\Employee;

class UserAdminController extends Controller
{
    /** Admin: manage roles, linked employee, and privileges for non-Owner / non-Admin users only. */
    public function index()
    {
        $users = User::with('employee.department', 'employee.designation')
            ->whereNotIn('role', [User::ROLE_OWNER, User::ROLE_ADMIN])
            ->orderBy('name')
            ->get();

        $employees = Employee::with('department', 'designation')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $roles = [
            User::ROLE_MANAGEMENT => 'Management',
            User::ROLE_HR => 'HR',
            User::ROLE_MANAGER => 'Manager',
            User::ROLE_ACCOUNTS => 'Accounts',
            User::ROLE_EMPLOYEE => 'Employee',
        ];

        $permissions = User::availablePermissions();

        return view('core::users.index', compact('users', 'employees', 'roles', 'permissions'));
    }

    public function update(Request $request)
    {
        $validKeys = array_keys(User::availablePermissions());

        $request->validate([
            'users' => 'array',
            'users.*.role' => 'required|string|in:management,hr,manager,accounts,employee',
            'users.*.employee_id' => 'nullable|integer|exists:employees,id',
            'users.*.permissions' => 'nullable|array',
            'users.*.permissions.*' => 'string|in:' . implode(',', $validKeys),
        ]);

        $data = $request->input('users', []);
        foreach ($data as $id => $values) {
            $user = User::find($id);
            if (! $user) {
                continue;
            }
            // Only update users that are in our manageable set (not owner, not admin).
            if ($user->isOwner() || $user->role === User::ROLE_ADMIN) {
                continue;
            }
            $user->role = $values['role'] ?? $user->role;
            $user->employee_id = isset($values['employee_id']) && $values['employee_id'] !== '' ? (int) $values['employee_id'] : null;
            $user->permissions = isset($values['permissions']) && is_array($values['permissions'])
                ? array_values(array_intersect($values['permissions'], $validKeys))
                : [];
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User roles, employee links and privileges updated.');
    }
}
