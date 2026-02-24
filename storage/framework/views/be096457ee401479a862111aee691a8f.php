

<?php $__env->startSection('title', 'Owner Portal'); ?>
<?php $__env->startSection('heading', 'Owner Portal'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl space-y-8">
    <p class="text-slate-600 dark:text-slate-400">Control which companies exist and which modules are available. This area is restricted to the owner role only.</p>

    <?php if(session('success')): ?>
    <div class="p-4 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Companies</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Manage companies from Organization → Companies. Here you configure which modules are enabled.</p>
        <ul class="space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <li class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <span class="font-medium text-slate-900 dark:text-slate-100"><?php echo e($c->name); ?></span>
                <a href="<?php echo e(route('company.index')); ?>" class="text-sm wise-link">Edit in Organization</a>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="text-slate-500 dark:text-slate-400">No companies yet. Add one from Organization → Companies.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Enable modules (global)</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">These settings apply app-wide. Uncheck a module to hide it from the sidebar and restrict access for all users.</p>
        <form method="POST" action="<?php echo e(route('owner.modules.update')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="company_id" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php $__currentLoopData = $moduleKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                    <input type="checkbox" name="modules[]" value="<?php echo e($key); ?>" <?php echo e(in_array($key, $globalModules, true) ? 'checked' : ''); ?>>
                    <span class="text-slate-800 dark:text-slate-200"><?php echo e($label); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save module settings</button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h2 class="wise-heading text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">Per-company modules (optional)</h2>
        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Select a company to override which modules are enabled for that company only. If not set, global settings above apply.</p>
        <form method="POST" action="<?php echo e(route('owner.modules.update')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company</label>
                <select name="company_id" class="w-full max-w-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2" required>
                    <option value="">Select company</option>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <?php $__currentLoopData = $moduleKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer">
                    <input type="checkbox" name="modules[]" value="<?php echo e($key); ?>">
                    <span class="text-slate-800 dark:text-slate-200"><?php echo e($label); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 wise-btn text-white rounded-lg">Save for this company</button>
            </div>
        </form>
    </div>

    <div class="text-sm text-slate-500 dark:text-slate-400">
        <strong>Note:</strong> When a module is disabled, its menu item is hidden and direct URL access is restricted. Only the owner can change these settings.
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/owner/index.blade.php ENDPATH**/ ?>