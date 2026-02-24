<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Services\SettingsService;

class CompanyLogoController extends Controller
{
    /**
     * Serve the company logo from storage (works without public/storage symlink).
     */
    public function show(): Response
    {
        $path = SettingsService::get('company_logo');
        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return response($file, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
