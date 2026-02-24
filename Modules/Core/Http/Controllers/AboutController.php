<?php
/**
 * Wise HRM – Modular HR Management System
 * Developer: M H Morshed
 * Copyright © 2025 M H Morshed. Built with Laravel.
 */

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;

class AboutController extends Controller
{
    public function index()
    {
        return view('core::about', [
            'appName' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
            'developer' => config('app.developer', 'M H Morshed'),
            'copyright' => config('app.copyright', 'Copyright © ' . date('Y') . ' M H Morshed. Built with Laravel.'),
            'logoPath' => file_exists(public_path('wt-logo.png')) ? asset('wt-logo.png') : null,
        ]);
    }
}
