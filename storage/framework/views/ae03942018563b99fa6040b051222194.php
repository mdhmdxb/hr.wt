<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Wise HRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' };</script>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900">
    <div class="min-h-full flex flex-col items-center justify-center py-12 px-4">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-6">Wise HRM</h1>
        <?php if(session('success')): ?>
            <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg max-w-md w-full"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8">
            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white px-3 py-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300">
                    <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400">Remember me</label>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/auth/login.blade.php ENDPATH**/ ?>