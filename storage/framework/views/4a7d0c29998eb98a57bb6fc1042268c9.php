
<?php
    $t = \Modules\Settings\Services\SettingsService::class;
    $themePrimary = $t::get('primary_color', '#4f46e5');
    $themeSecondary = $t::get('secondary_color', '#6366f1');
    $themeAccent = $t::get('accent_color', '#818cf8');
    $themeLink = $t::get('link_color', '#4f46e5');
    $themeButtonBg = $t::get('button_bg', '#4f46e5');
    $themeSidebarActiveBg = $t::get('sidebar_active_bg', 'rgba(79, 70, 229, 0.1)');
    $themeSidebarActiveText = $t::get('sidebar_active_text', '#4f46e5');
    $themeFont = $t::allowedFontValue($t::get('font_family', 'system-ui'));
    $themeHeadingFont = $t::allowedFontValue($t::get('heading_font', 'system-ui'));
    $themeRadius = $t::get('border_radius', '0.5rem');
?>
<!DOCTYPE html>
<?php $isDark = request()->cookie('theme') === 'dark'; ?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full <?php echo e($isDark ? 'dark' : ''); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="app-path" content="<?php echo e(rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?: '/', '/') ?: '/'); ?>">
    <link rel="icon" href="<?php echo e(route('app.favicon')); ?>" type="image/png" sizes="any">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Wise HRM</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&family=Lato:wght@400;700&family=Source+Sans+3:wght@400;600;700&family=Nunito:wght@400;600;700&family=Poppins:wght@400;500;600;700&family=Merriweather:wght@400;700&family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <style>
        :root {
            --wise-primary: <?php echo e($themePrimary); ?>;
            --wise-secondary: <?php echo e($themeSecondary); ?>;
            --wise-accent: <?php echo e($themeAccent); ?>;
            --wise-link: <?php echo e($themeLink); ?>;
            --wise-button-bg: <?php echo e($themeButtonBg); ?>;
            --wise-sidebar-active-bg: <?php echo e($themeSidebarActiveBg); ?>;
            --wise-sidebar-active-text: <?php echo e($themeSidebarActiveText); ?>;
            --wise-font: <?php echo \Modules\Settings\Services\SettingsService::fontKeyToCss($themeFont); ?>;
            --wise-heading-font: <?php echo \Modules\Settings\Services\SettingsService::fontKeyToCss($themeHeadingFont); ?>;
            --wise-radius: <?php echo e($themeRadius); ?>;
        }
        body { font-family: var(--wise-font); }
        h1, h2, h3, .wise-heading { font-family: var(--wise-heading-font); }
        .wise-btn {
            background-color: var(--wise-button-bg);
            border-radius: var(--wise-radius);
        }
        .wise-btn:hover { filter: brightness(1.05); }
        .wise-link, a.wise-link { color: var(--wise-link); }
        .wise-link:hover { text-decoration: underline; }
        .wise-sidebar-link {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-radius: var(--wise-radius);
            color: rgb(51 65 85); transition: background-color 0.15s, color 0.15s;
        }
        .dark .wise-sidebar-link { color: rgb(203 213 225); }
        .wise-sidebar-link:hover { background-color: rgb(241 245 249); }
        .dark .wise-sidebar-link:hover { background-color: rgb(51 65 80); }
        .wise-sidebar-link.wise-sidebar-active {
            background: var(--wise-sidebar-active-bg);
            color: var(--wise-sidebar-active-text) !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<?php $appPath = rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?: '/', '/') ?: '/'; ?>
<body class="h-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100" data-app-path="<?php echo e($appPath); ?>">
    <div class="min-h-full flex" x-data="{ dark: <?php echo json_encode($isDark, 15, 512) ?> }" x-init="
        document.documentElement.classList.toggle('dark', dark);
        $watch('dark', function(v) {
            document.documentElement.classList.toggle('dark', v);
            var p = document.body.getAttribute('data-app-path') || '/';
            document.cookie = 'theme=' + (v ? 'dark' : 'light') + ';path=' + p + ';max-age=31536000';
        });
    ">
        
        <aside class="w-64 flex flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <?php
                    $logo = \Modules\Settings\Services\SettingsService::get('company_logo');
                    $logoUrl = $logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo)
                        ? route('app.logo')
                        : null;
                ?>
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="Logo" class="h-10 object-contain max-w-full">
                <?php else: ?>
                    <span class="text-xl font-bold wise-link">Wise HRM</span>
                <?php endif; ?>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <?php echo $__env->make('core::partials.sidebar-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            
            <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800 dark:text-slate-100"><?php echo $__env->yieldContent('heading', 'Dashboard'); ?></h1>
                </div>
                <div class="flex items-center gap-4">
                    
                    <?php $unread = auth()->user()->unreadNotifications()->take(5)->get(); $unreadCount = auth()->user()->unreadNotifications()->count(); ?>
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700" aria-label="Notifications">
                            <span class="text-xl">🔔</span>
                            <?php if($unreadCount > 0): ?>
                            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"><?php echo e($unreadCount > 9 ? '9+' : $unreadCount); ?></span>
                            <?php endif; ?>
                        </button>
                        <div x-show="open" x-cloak class="absolute right-0 mt-2 w-80 rounded-xl bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50 max-h-96 overflow-y-auto">
                            <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                                <span class="font-semibold text-slate-800 dark:text-slate-100">Notifications</span>
                                <?php if($unreadCount > 0): ?>
                                <form method="POST" action="<?php echo e(route('notifications.mark-read')); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-xs wise-link">Mark all read</button>
                                </form>
                                <?php endif; ?>
                            </div>
                            <?php $__empty_1 = true; $__currentLoopData = $unread; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $data = $n->data; ?>
                            <a href="<?php echo e(route('notifications.read', $n->id)); ?>" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-left">
                                <p class="text-sm text-slate-700 dark:text-slate-300"><?php echo e(\Illuminate\Support\Arr::get($data, 'message', 'Notification')); ?></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?php echo e($n->created_at->diffForHumans()); ?></p>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">No new notifications.</p>
                            <?php endif; ?>
                            <?php if($unreadCount > 0): ?>
                            <div class="px-4 py-2 border-t border-slate-200 dark:border-slate-700">
                                <a href="<?php echo e(route('notifications.index')); ?>" class="text-sm wise-link">View all</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <button type="button" @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); (function(){ var p = document.body.getAttribute('data-app-path') || '/'; document.cookie = 'theme=' + (dark ? 'dark' : 'light') + ';path=' + p + ';max-age=31536000'; })();"
                        class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700" aria-label="Toggle theme">
                        <span x-show="!dark">🌙</span>
                        <span x-show="dark" x-cloak>☀️</span>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600 dark:text-slate-400"><?php echo e(auth()->user()->name); ?></span>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-sm wise-link">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-auto">
                <?php if(session('success')): ?>
                    <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg"><?php echo e(session('error')); ?></div>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\Wise-HRM\Modules\Core\Providers/../Resources/views/layouts/app.blade.php ENDPATH**/ ?>