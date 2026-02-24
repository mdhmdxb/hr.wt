

<?php $__env->startSection('title', 'About'); ?>
<?php $__env->startSection('heading', 'About this app'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-8 md:p-10 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 mb-6">
            <?php if(!empty($logoPath)): ?>
            <img src="<?php echo e($logoPath); ?>" alt="<?php echo e($appName); ?>" class="w-24 h-24 sm:w-28 sm:h-28 object-contain flex-shrink-0 rounded-xl border border-slate-200 dark:border-slate-600">
            <?php endif; ?>
            <div class="flex-1 text-center sm:text-left">
                <h2 class="wise-heading text-2xl font-bold text-slate-900 dark:text-slate-100"><?php echo e($appName); ?></h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Version <?php echo e($version); ?></p>
            </div>
        </div>
        <p class="text-slate-600 dark:text-slate-300 mb-6">
            Modular HR Management System. Employees, attendance, leave, payroll, and reports — all in one place.
        </p>
        <dl class="space-y-4 text-slate-700 dark:text-slate-300">
            <div>
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Developer</dt>
                <dd class="mt-1 font-medium"><?php echo e($developer); ?></dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Copyright</dt>
                <dd class="mt-1"><?php echo e($copyright); ?></dd>
            </div>
        </dl>
        <p class="mt-6 text-sm text-slate-500 dark:text-slate-400">
            Built with <a href="https://laravel.com" target="_blank" rel="noopener" class="wise-link">Laravel</a>.
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('core::layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/about.blade.php ENDPATH**/ ?>