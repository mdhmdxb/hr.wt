@php
$user = auth()->user();
$isOwner = $user->isOwner();
$isAdmin = $user->isAdmin();
$isHr = $user->isHr();
$isManager = $user->isManager();
$isAccounts = $user->isAccounts();
$mod = function($key) { return \Modules\Core\Models\Setting::isModuleEnabled($key); };
$perm = function($key) use ($user) { return $user->hasPermission($key); };

$groups = [];

// Owner portal (owner only)
if ($isOwner) {
    $groups[] = ['label' => 'Owner', 'items' => [
        ['route' => 'owner.index', 'label' => 'Owner Portal', 'icon' => '🔐'],
    ]];
}

// Main
$main = [['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '📊']];
if ($isAdmin) {
    $main[] = ['route' => 'dashboard.executive', 'label' => 'Executive', 'icon' => '🎯'];
}
$groups[] = ['label' => 'Main', 'items' => $main];

// People (including Designations) — role or privilege
$people = [];
if ($mod('employees') && ($isAdmin || $isHr || $perm('manage_employees'))) {
    $people[] = ['route' => 'employee.index', 'label' => 'Employees', 'icon' => '👥'];
}
if ($mod('documents') && ($isAdmin || $isHr || $perm('manage_documents'))) {
    $people[] = ['route' => 'documents.index', 'label' => 'Documents', 'icon' => '📂'];
}
if ($mod('documents') && $user->employee_id && !$isAdmin && !$isHr && !$perm('manage_documents')) {
    $people[] = ['route' => 'my-documents.index', 'label' => 'My documents', 'icon' => '📂'];
}
if ($mod('recruitment') && ($isAdmin || $isHr || $perm('manage_employees'))) {
    $people[] = ['route' => 'recruitment.index', 'label' => 'Recruitment', 'icon' => '📋'];
}
if ($mod('designations') && ($isAdmin || $isHr || $perm('manage_employees'))) {
    $people[] = ['route' => 'designation.index', 'label' => 'Designations', 'icon' => '👔'];
}
if ($isAdmin || $isHr || $perm('manage_employees')) {
    $people[] = ['route' => 'department.index', 'label' => 'Departments', 'icon' => '🏢'];
}
if (!empty($people)) {
    $groups[] = ['label' => 'People', 'items' => $people];
}

// Time — role or privilege
$time = [];
if ($mod('attendance') && ($isAdmin || $isHr || $perm('manage_attendance'))) {
    $time[] = ['route' => 'attendance.index', 'label' => 'Attendance', 'icon' => '🕐'];
}
if ($mod('attendance') && $user->employee_id && !$isAdmin && !$isHr && !$perm('manage_attendance')) {
    $time[] = ['route' => 'attendance.my', 'label' => 'My Attendance', 'icon' => '🕐'];
}
if ($mod('leave') && $user->employee_id && !$isAdmin && !$isHr && !$perm('manage_leave')) {
    $time[] = ['route' => 'my-leave.index', 'label' => 'My Leave', 'icon' => '📅'];
}
if ($mod('leave') && ($isAdmin || $isHr || $isManager || $perm('manage_leave'))) {
    $time[] = ['route' => 'leave.index', 'label' => 'Leave', 'icon' => '📅'];
    if ($isAdmin || $isHr || $perm('manage_leave')) {
        $time[] = ['route' => 'leave.history.index', 'label' => 'Leave history', 'icon' => '📋'];
    }
}
if ($isAdmin || $isHr || $perm('manage_leave')) {
    $time[] = ['route' => 'holidays.index', 'label' => 'Public Holidays', 'icon' => '🏖️'];
}
if (!empty($time)) {
    $groups[] = ['label' => 'Time', 'items' => $time];
}

// Finance (Payroll only) — role or privilege
$finance = [];
if ($mod('payroll') && ($isAdmin || $isAccounts || $perm('manage_payroll'))) {
    $finance[] = ['route' => 'payroll.index', 'label' => 'Payroll', 'icon' => '💰'];
}
if (!empty($finance)) {
    $groups[] = ['label' => 'Finance', 'items' => $finance];
}

// Reports & Templates — role or privilege
$reports = [];
if ($mod('reports') && ($isAdmin || $isHr || $isAccounts || $perm('manage_reports'))) {
    $reports[] = ['route' => 'reports.index', 'label' => 'Reports', 'icon' => '📈'];
}
if ($mod('templates') && ($isAdmin || $perm('manage_templates'))) {
    $reports[] = ['route' => 'templates.index', 'label' => 'Templates – Reports', 'icon' => '📄'];
    $reports[] = ['route' => 'email-templates.index', 'label' => 'Templates – Email', 'icon' => '✉️'];
}
if (!empty($reports)) {
    $groups[] = ['label' => 'Reports', 'items' => $reports];
}

// Organization — role or privilege
$org = [];
if ($mod('companies') && ($isAdmin || $perm('manage_organization'))) {
    $org[] = ['route' => 'company.index', 'label' => 'Companies', 'icon' => '🏛️'];
}
if ($mod('branches') && ($isAdmin || $perm('manage_organization'))) {
    $org[] = ['route' => 'branch.index', 'label' => 'Branches', 'icon' => '🏢'];
}
if ($mod('sites') && ($isAdmin || $perm('manage_organization'))) {
    $org[] = ['route' => 'site.index', 'label' => 'Sites', 'icon' => '📍'];
}
if ($mod('projects') && ($isAdmin || $isHr || $perm('manage_organization'))) {
    $org[] = ['route' => 'projects.index', 'label' => 'Projects', 'icon' => '📋'];
}
if (!empty($org)) {
    $groups[] = ['label' => 'Organization', 'items' => $org];
}

// Assets — role or privilege
if ($mod('assets') && ($isAdmin || $isHr || $perm('manage_documents'))) {
    $groups[] = ['label' => 'Assets', 'items' => [
        ['route' => 'assets.index', 'label' => 'Assets', 'icon' => '📱'],
    ]];
}

// System — Users & roles only for Admin/Management; Settings for Admin/HR or privilege
$system = [];
if ($isAdmin || $isHr || $perm('manage_settings')) {
    $system[] = ['route' => 'settings.general', 'label' => 'Settings', 'icon' => '⚙️'];
}
if ($isAdmin) {
    $system[] = ['route' => 'users.index', 'label' => 'Users & roles', 'icon' => '👤'];
}
$system[] = ['route' => 'about', 'label' => 'About', 'icon' => 'ℹ️'];
$groups[] = ['label' => 'System', 'items' => $system];
@endphp

@php
$sidebarGroupHasActive = function ($items) {
    foreach ($items as $it) {
        $r = $it['route'];
        if (request()->routeIs($r) || request()->routeIs(str_replace('.index', '.*', $r))) {
            return true;
        }
    }
    return false;
};
@endphp
<nav class="space-y-1">
    @foreach($groups as $i => $group)
    @php $groupOpen = $sidebarGroupHasActive($group['items']); $groupLabel = $group['label']; @endphp
    <div class="sidebar-group" x-data="{ open: @json($groupOpen) }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors rounded-lg">
            <span>{{ $groupLabel }}</span>
            <span class="text-xs transition-transform duration-200 origin-center" :class="open ? 'rotate-180' : ''">▼</span>
        </button>
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="overflow-hidden">
            <ul class="mt-0.5 space-y-0.5 pl-1 border-l-2 border-slate-200 dark:border-slate-600 ml-2">
                @foreach($group['items'] as $item)
                @php
                    $routeName = $item['route'];
                    $active = request()->routeIs($routeName) || request()->routeIs(str_replace('.index', '.*', $routeName));
                    $itemIcon = $item['icon'];
                    $itemLabel = $item['label'];
                @endphp
                <li>
                    <a href="{{ route($routeName) }}" class="wise-sidebar-link {{ $active ? 'wise-sidebar-active' : '' }} block">
                        <span>{{ $itemIcon }}</span>
                        <span>{{ $itemLabel }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endforeach
</nav>
