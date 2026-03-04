

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('heading', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $hour = (int) date('H');
    if ($hour < 12) { $greeting = 'Good morning'; } elseif ($hour < 17) { $greeting = 'Good afternoon'; } else { $greeting = 'Good evening'; }
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $isHr = $user->isHr();
    $isAccounts = $user->isAccounts();
    $isManager = $user->isManager();
    $canSeeTeamDashboard = $isAdmin || $isHr || $isManager || $user->hasPermission('manage_leave');
?>


<?php $isEmployeeOnly = auth()->user()->employee_id && !$isAdmin && !$isHr && !$isAccounts; ?>
<div class="relative overflow-hidden rounded-2xl mb-6 p-8 md:p-10 text-white shadow-xl" style="background: linear-gradient(135deg, var(--wise-primary) 0%, var(--wise-secondary) 50%, var(--wise-accent) 100%);">
    <div class="relative z-10">
        <p class="text-white/90 text-sm font-medium uppercase tracking-wider mb-1"><?php echo e($greeting); ?></p>
        <h2 class="wise-heading text-3xl md:text-4xl font-bold mb-2"><?php echo e($user->name); ?></h2>
        <p class="text-white/90 max-w-xl"><?php if($isEmployeeOnly): ?> Your dashboard. View your attendance and activity. <?php else: ?> Here’s what’s happening across your organization today. <?php endif; ?></p>
    </div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
    <?php if($isEmployeeOnly && ($isBirthday ?? false) && isset($myEmployee)): ?>
        <div class="mt-4 inline-flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm animate-pulse">
            <div class="text-2xl">🎉</div>
            <div>
                <p class="wise-heading text-sm font-semibold">Happy birthday, <?php echo e($myEmployee->full_name); ?>!</p>
                <p class="text-xs text-white/80">Wishing you a wonderful year ahead from Wise HRM.</p>
            </div>
        </div>
    <?php endif; ?>
</div>


<?php if(auth()->user()->employee_id && ($showIndividualCheckin ?? false)): ?>
<?php
    $att = $todayAttendance ?? null;
    $canCheckIn = !$att;
    $canCheckOut = $att && $att->check_in_at && !$att->check_out_at;
?>
<div class="mb-6 p-6 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50">
    <h3 class="wise-heading text-sm font-semibold text-slate-800 dark:text-slate-100 mb-3">Today's attendance</h3>
    <?php if($att): ?>
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Check-in: <strong><?php echo e($att->check_in_at ? \Carbon\Carbon::parse($att->check_in_at)->format('h:i A') : '—'); ?></strong>
        <?php if($att->check_out_at): ?>
            · Check-out: <strong><?php echo e(\Carbon\Carbon::parse($att->check_out_at)->format('h:i A')); ?></strong>
        <?php endif; ?>
    </p>
    <?php endif; ?>
    <div class="flex flex-col sm:flex-row gap-3">
        <?php if($canCheckIn): ?>
        <form method="POST" action="<?php echo e(route('attendance.self.check-in')); ?>" class="flex-1">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full sm:flex-1 py-4 px-6 rounded-xl font-semibold text-white text-lg shadow-lg hover:shadow-xl transition-all min-h-14 touch-manipulation" style="background: linear-gradient(135deg, var(--wise-primary), var(--wise-secondary));">
                Check in
            </button>
        </form>
        <?php endif; ?>
        <?php if($canCheckOut): ?>
        <form method="POST" action="<?php echo e(route('attendance.self.check-out')); ?>" class="flex-1">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full sm:flex-1 py-4 px-6 rounded-xl font-semibold text-white text-lg shadow-lg hover:shadow-xl transition-all min-h-14 touch-manipulation bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-700">
                Check out
            </button>
        </form>
        <?php endif; ?>
        <?php if(!$canCheckIn && !$canCheckOut && $att): ?>
        <p class="text-slate-500 dark:text-slate-400 py-2">You have completed today's attendance.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


<?php if($isAdmin || $isHr): ?>

<form method="GET" action="<?php echo e(route('dashboard')); ?>" id="dashboard-filter-form" class="mb-6 p-4 bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50">
    <h3 class="wise-heading text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Filter by</h3>
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Company</label>
            <select name="company_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="var f=this.form; f.branch_id.value=''; f.site_id.value=''; f.department_id.value=''; f.submit();">
                <option value="">All companies</option>
                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e((\Illuminate\Support\Arr::get($filter, 'company_id', '')) == $c->id ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Branch</label>
            <select name="branch_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="var f=this.form; f.site_id.value=''; f.department_id.value=''; f.submit();">
                <option value="">All branches</option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b->id); ?>" <?php echo e((\Illuminate\Support\Arr::get($filter, 'branch_id', '')) == $b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Site</label>
            <select name="site_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All sites</option>
                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e((\Illuminate\Support\Arr::get($filter, 'site_id', '')) == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Department</label>
            <select name="department_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All departments</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>" <?php echo e((\Illuminate\Support\Arr::get($filter, 'department_id', '')) == $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php if(isset($projects) && $projects->isNotEmpty()): ?>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Project</label>
            <select name="project_id" class="rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2 text-sm min-w-40" onchange="this.form.submit()">
                <option value="">All projects</option>
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php echo e((\Illuminate\Support\Arr::get($filter, 'project_id', '')) == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>
        <?php if(\Illuminate\Support\Arr::get($filter, 'company_id') ?: \Illuminate\Support\Arr::get($filter, 'branch_id') ?: \Illuminate\Support\Arr::get($filter, 'site_id') ?: \Illuminate\Support\Arr::get($filter, 'department_id') ?: \Illuminate\Support\Arr::get($filter, 'project_id')): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50">Clear</a>
        <?php endif; ?>
    </div>
</form>
<?php endif; ?>


<?php if($canSeeTeamDashboard): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Employees</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1"><?php echo e(\Illuminate\Support\Arr::get($stats, 'employees', 0)); ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Active</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-slate-100 dark:bg-slate-700" style="color: var(--wise-primary);">👥</div>
        </div>
        <?php if($isAdmin || $isHr): ?>
        <a href="<?php echo e(route('employee.index')); ?>" class="mt-4 inline-block text-sm font-medium wise-link">View all →</a>
        <?php endif; ?>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Today’s attendance</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1"><?php echo e(\Illuminate\Support\Arr::get($stats, 'attendance_today', 0)); ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Records</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">🕐</div>
        </div>
        <?php if($isAdmin || $isHr): ?>
        <a href="<?php echo e(route('attendance.index')); ?>" class="mt-4 inline-block text-sm font-medium wise-link">View / record →</a>
        <?php endif; ?>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pending leave</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1"><?php echo e(\Illuminate\Support\Arr::get($stats, 'pending_leave', 0)); ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Awaiting approval</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">📅</div>
        </div>
        <?php if($isAdmin || $isHr || $isManager || $user->hasPermission('manage_leave')): ?>
        <a href="<?php echo e(route('leave.index')); ?>?status=pending" class="mt-4 inline-block text-sm font-medium wise-link">Review →</a>
        <?php endif; ?>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Payroll this month</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-slate-100 mt-1"><?php echo e(\Illuminate\Support\Arr::get($stats, 'payroll_this_month', 0)); ?></p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Runs</p>
            </div>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">💰</div>
        </div>
        <?php if($isAdmin || $isAccounts): ?>
        <a href="<?php echo e(route('payroll.index')); ?>" class="mt-4 inline-block text-sm font-medium wise-link">View payroll →</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


<?php if($isAdmin || $isHr): ?>
<?php
    $gridCardKeys = ['assets_expiring_soon', 'assets_expired', 'documents_expiring_soon', 'documents_expired', 'upcoming_vacations', 'birthdays'];
    $gridOrder = array_values(array_intersect($dashboardCardOrder ?? [], $gridCardKeys));
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <?php $__currentLoopData = $gridOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cardKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($cardKey === 'assets_expiring_soon' && isset($assetsExpiringSoon) && $assetsExpiringSoon->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Assets expiring soon</h3>
        <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
            <?php $__currentLoopData = $assetsExpiringSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(route('assets.show', $a)); ?>" class="wise-link font-medium"><?php echo e($a->name); ?></a>
                — <?php echo e($a->expiry_date->format('Y-m-d')); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('assets.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View all assets →</a>
    </div>
    <?php elseif($cardKey === 'assets_expired' && isset($assetsExpired) && $assetsExpired->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Assets already expired</h3>
        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
            <?php $__currentLoopData = $assetsExpired; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(route('assets.show', $a)); ?>" class="wise-link font-medium"><?php echo e($a->name); ?></a>
                — <?php echo e($a->expiry_date->format('Y-m-d')); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('assets.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View all assets →</a>
    </div>
    <?php elseif($cardKey === 'documents_expiring_soon' && isset($documentsExpiringSoon) && $documentsExpiringSoon->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Documents expiring soon</h3>
        <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
            <?php $__currentLoopData = $documentsExpiringSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(route('documents.show', $doc)); ?>" class="wise-link font-medium"><?php echo e($doc->employee->full_name ?? '—'); ?></a>
                — <?php echo e(\Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type); ?> — <?php echo e($doc->expiry_date->format('Y-m-d')); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('documents.index', ['expiring' => 1])); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View documents →</a>
    </div>
    <?php elseif($cardKey === 'documents_expired' && isset($documentsExpired) && $documentsExpired->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Documents already expired</h3>
        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
            <?php $__currentLoopData = $documentsExpired; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(route('documents.show', $doc)); ?>" class="wise-link font-medium"><?php echo e($doc->employee->full_name ?? '—'); ?></a>
                — <?php echo e(\Modules\Core\Models\EmployeeDocument::typeOptions()[$doc->type] ?? $doc->type); ?> — <?php echo e($doc->expiry_date->format('Y-m-d')); ?>

            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('documents.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View documents →</a>
    </div>
    <?php elseif($cardKey === 'upcoming_vacations' && isset($upcomingLeaves) && $upcomingLeaves->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-sky-200 mb-2">Upcoming vacations</h3>
        <ul class="text-sm text-sky-800 dark:text-sky-100 space-y-1">
            <?php $__currentLoopData = $upcomingLeaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <span class="font-medium"><?php echo e($lr->employee->full_name ?? '—'); ?></span>
                — <?php echo e($lr->start_date->format('Y-m-d')); ?> → <?php echo e($lr->end_date->format('Y-m-d')); ?>

                <?php if($lr->leaveType): ?>
                    <span class="text-xs text-sky-700 dark:text-sky-300">(<?php echo e($lr->leaveType->name); ?>)</span>
                <?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('leave.index')); ?>?status=approved" class="inline-block mt-2 text-sm font-medium wise-link">View all leave →</a>
    </div>
    <?php elseif($cardKey === 'birthdays' && isset($upcomingBirthdays) && $upcomingBirthdays->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800">
        <h3 class="text-sm font-semibold text-pink-800 dark:text-pink-100 mb-2">Birthdays</h3>
        <ul class="text-sm text-pink-800 dark:text-pink-100 space-y-1">
            <?php $__currentLoopData = $upcomingBirthdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $emp = $row['employee']; $date = $row['next_birthday']; $isToday = $row['is_today'] ?? false; ?>
            <li>
                <span class="font-medium"><?php echo e($emp->full_name); ?></span>
                — <?php if($isToday): ?><span class="font-medium">Today</span><?php else: ?><?php echo e($date->format('M d')); ?><?php endif; ?>
                <?php if($emp->branch): ?> <span class="text-xs text-pink-700 dark:text-pink-300">(<?php echo e($emp->branch->name); ?>)</span> <?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php if(!$isAdmin && !$isHr && $canSeeTeamDashboard): ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <?php if(isset($upcomingLeaves) && $upcomingLeaves->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800">
        <h3 class="text-sm font-semibold text-sky-800 dark:text-sky-200 mb-2">Your team – upcoming vacations</h3>
        <ul class="text-sm text-sky-800 dark:text-sky-100 space-y-1">
            <?php $__currentLoopData = $upcomingLeaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <span class="font-medium"><?php echo e($lr->employee->full_name ?? '—'); ?></span>
                — <?php echo e($lr->leaveType->name ?? 'Leave'); ?>

                (<?php echo e($lr->start_date->format('Y-m-d')); ?> → <?php echo e($lr->end_date->format('Y-m-d')); ?>)
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('leave.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">Open leave list →</a>
    </div>
    <?php endif; ?>
    <?php if(isset($upcomingBirthdays) && $upcomingBirthdays->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800">
        <h3 class="text-sm font-semibold text-purple-800 dark:text-purple-200 mb-2">Your team – upcoming birthdays</h3>
        <ul class="text-sm text-purple-800 dark:text-purple-100 space-y-1">
            <?php $__currentLoopData = $upcomingBirthdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php /** @var \Modules\Core\Models\Employee $emp */ $emp = $row['employee']; $d = $row['next_birthday']; ?>
            <li>
                <span class="font-medium"><?php echo e($emp->full_name); ?></span>
                — <?php echo e($d->format('Y-m-d')); ?> <?php if(!empty($row['is_today'])): ?> <span class="text-xs font-semibold">(Today)</span> <?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if($isAdmin || $isHr): ?>
<?php if(in_array('calendar', $dashboardCardOrder) && !empty($calendarWeeks) && count($calendarWeeks) > 0): ?>
<?php
    $calMonth = (int) ($calendarMonthNum ?? date('n'));
    $calYear = (int) ($calendarYear ?? date('Y'));
    $calPrev = $calMonth <= 1 ? [$calYear - 1, 12] : [$calYear, $calMonth - 1];
    $calNext = $calMonth >= 12 ? [$calYear + 1, 1] : [$calYear, $calMonth + 1];
    $q = request()->query();
    $prevUrl = route('dashboard', array_merge($q, ['calendar_year' => $calPrev[0], 'calendar_month' => $calPrev[1]]));
    $nextUrl = route('dashboard', array_merge($q, ['calendar_year' => $calNext[0], 'calendar_month' => $calNext[1]]));
?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-6">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Calendar</h3>
    <div class="flex flex-wrap items-center gap-4 mb-4">
        <a href="<?php echo e($prevUrl); ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50">← Previous</a>
        <span class="font-medium text-slate-800 dark:text-slate-100"><?php echo e($calendarMonth ?? now()->format('F Y')); ?></span>
        <a href="<?php echo e($nextUrl); ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50">Next →</a>
        <form method="get" action="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2 flex-wrap">
            <?php $__currentLoopData = request()->query(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php if($k !== 'calendar_month' && $k !== 'calendar_year'): ?><input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>"><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="calendar_month" class="rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" onchange="this.form.submit()">
                <?php $__currentLoopData = ['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m); ?>" <?php echo e((isset($calendarMonthNum) && (int)$calendarMonthNum === (int)$m) ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="calendar_year" class="rounded border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-2 py-1 text-sm" onchange="this.form.submit()">
                <?php for($y = (isset($today) ? $today->year : date('Y')) - 2; $y <= (isset($today) ? $today->year : date('Y')) + 2; $y++): ?>
                <option value="<?php echo e($y); ?>" <?php echo e((isset($calendarYear) && (int)$calendarYear === $y) ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>
    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Gregorian / Hijri (Lunar) <?php if(!empty($companyCountry)): ?> · Country: <?php echo e(\Modules\Core\Helpers\CountryList::codes()[$companyCountry] ?? $companyCountry); ?> <?php endif; ?></p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-600">
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Sun</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Mon</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Tue</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Wed</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Thu</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Fri</th>
                    <th class="text-left py-2 px-1 text-slate-600 dark:text-slate-400 font-medium">Sat</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $calendarWeeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <?php $__currentLoopData = $week; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="align-top py-2 px-1 w-[14%]">
                        <div class="min-h-[3.5rem] rounded-lg p-2 <?php echo e($day['in_month'] ? 'bg-slate-50 dark:bg-slate-700/30' : 'bg-slate-100/50 dark:bg-slate-800/50'); ?> <?php echo e($day['is_today'] ? 'ring-2 ring-indigo-500 dark:ring-indigo-400' : ''); ?> <?php echo e($day['is_holiday'] ? 'bg-amber-100 dark:bg-amber-900/30' : ''); ?>">
                            <div class="font-semibold text-slate-800 dark:text-slate-200"><?php echo e($day['day']); ?></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($day['hijri']); ?></div>
                            <?php if($day['holiday_name']): ?>
                            <div class="text-xs font-medium text-amber-700 dark:text-amber-300 mt-0.5 truncate" title="<?php echo e($day['holiday_name']); ?>"><?php echo e(Str::limit($day['holiday_name'], 12)); ?></div>
                            <?php endif; ?>
                        </div>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <?php if(in_array('public_holidays_month', $dashboardCardOrder) && isset($publicHolidaysThisMonth) && $publicHolidaysThisMonth->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
        <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Public holidays this month</h3>
        <ul class="text-sm text-amber-800 dark:text-amber-100 space-y-1">
            <?php $__currentLoopData = $publicHolidaysThisMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <span class="font-medium"><?php echo e($h->name); ?></span>
                — <?php echo e($h->date->format('M j')); ?><?php if($h->end_date && $h->end_date->ne($h->date)): ?> – <?php echo e($h->end_date->format('M j')); ?><?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('holidays.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    <?php elseif(in_array('public_holidays_month', $dashboardCardOrder)): ?>
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">Public holidays this month</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">None set for this month. Set company country in Settings and add holidays.</p>
        <a href="<?php echo e(route('holidays.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    <?php endif; ?>

    <?php if(in_array('upcoming_public_holidays', $dashboardCardOrder) && isset($upcomingPublicHolidays) && $upcomingPublicHolidays->isNotEmpty()): ?>
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
        <h3 class="text-sm font-semibold text-emerald-800 dark:text-emerald-200 mb-2">Upcoming public holidays</h3>
        <ul class="text-sm text-emerald-800 dark:text-emerald-100 space-y-1">
            <?php $__currentLoopData = $upcomingPublicHolidays->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <span class="font-medium"><?php echo e($h->name); ?></span>
                — <?php echo e($h->date->format('M j, Y')); ?><?php if($h->end_date && $h->end_date->ne($h->date)): ?> – <?php echo e($h->end_date->format('M j')); ?><?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('holidays.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View all →</a>
    </div>
    <?php elseif(in_array('upcoming_public_holidays', $dashboardCardOrder)): ?>
    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">Upcoming public holidays</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">None in the next 60 days. Add holidays in Public Holidays.</p>
        <a href="<?php echo e(route('holidays.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">Manage public holidays →</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if(($isAdmin || $isHr || $isAccounts) && in_array('quick_actions', $dashboardCardOrder)): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6 mb-8">
    <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Quick actions</h3>
    <div class="flex flex-wrap gap-3">
        <?php if($isAdmin || $isHr): ?>
        <a href="<?php echo e(route('attendance.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 wise-btn text-white rounded-lg text-sm font-medium">Record attendance</a>
        <a href="<?php echo e(route('leave.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 wise-btn text-white rounded-lg text-sm font-medium">Submit leave request</a>
        <a href="<?php echo e(route('employee.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">Add employee</a>
        <?php endif; ?>
        <?php if($isAdmin || $isAccounts): ?>
        <a href="<?php echo e(route('payroll.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">New payroll run</a>
        <?php endif; ?>
        <?php if($isAdmin): ?>
        <a href="<?php echo e(route('settings.general')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">Settings</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>


<?php if($isAdmin || $isHr || $isAccounts): ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <?php if($recentActivity->isNotEmpty()): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Recent activity</h3>
        <ul class="space-y-3">
            <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex items-start gap-3 text-sm">
                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background: var(--wise-primary);"></span>
                <div>
                    <span class="text-slate-700 dark:text-slate-300"><?php echo e($log->description ?? $log->action); ?></span>
                    <span class="text-slate-500 dark:text-slate-400 text-xs ml-1"><?php echo e($log->created_at->diffForHumans()); ?></span>
                </div>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Getting started</h3>
        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
            <li class="flex items-center gap-2">✓ Add employees and assign them to branches</li>
            <li class="flex items-center gap-2">✓ Record attendance and manage leave requests</li>
            <li class="flex items-center gap-2">✓ Run payroll for your team</li>
            <li class="flex items-center gap-2">✓ Customize logo and theme in Settings</li>
        </ul>
    </div>
    <?php endif; ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">At a glance</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Your HR hub is ready. Use the quick actions above or the sidebar to manage employees, attendance, leave, and payroll. Everything stays consistent with your company settings and theme.</p>
    </div>
</div>
<?php else: ?>

<?php if(auth()->user()->employee_id): ?>
<?php
    $myUpcomingLeave = \Modules\Leave\Models\LeaveRequest::with('leaveType')
        ->where('employee_id', auth()->user()->employee_id)
        ->where('status', \Modules\Leave\Models\LeaveRequest::STATUS_APPROVED)
        ->whereDate('end_date', '>=', now()->toDateString())
        ->orderBy('start_date')
        ->take(3)
        ->get();
?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Your space</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Use the sidebar to open My Attendance, My Leave (request time off), and About. Contact your manager or HR if you need help.</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200/50 dark:border-slate-700/50 p-6">
        <h3 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Your upcoming leave</h3>
        <?php if($myUpcomingLeave->isEmpty()): ?>
            <p class="text-sm text-slate-500 dark:text-slate-400">No approved upcoming leave yet.</p>
        <?php else: ?>
        <ul class="text-sm text-slate-700 dark:text-slate-300 space-y-1">
            <?php $__currentLoopData = $myUpcomingLeave; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <?php echo e($lr->start_date->format('Y-m-d')); ?> → <?php echo e($lr->end_date->format('Y-m-d')); ?>

                <?php if($lr->leaveType): ?>
                    <span class="text-xs text-slate-500 dark:text-slate-400">(<?php echo e($lr->leaveType->name); ?>)</span>
                <?php endif; ?>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <a href="<?php echo e(route('my-leave.index')); ?>" class="inline-block mt-2 text-sm font-medium wise-link">View all my leave →</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/dashboard.blade.php ENDPATH**/ ?>