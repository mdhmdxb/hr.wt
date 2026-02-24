<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Services\SettingsService;

class FaviconController extends Controller
{
    public function show(): Response
    {
        $path = SettingsService::get('favicon');
        if ($path && Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path);
            return response($file, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
        if (file_exists(public_path('wt-logo.png'))) {
            return response()->file(public_path('wt-logo.png'))->withHeaders([
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
        $logoPath = SettingsService::get('company_logo');
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $file = Storage::disk('public')->get($logoPath);
            $mime = Storage::disk('public')->mimeType($logoPath);
            return response($file, 200, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
        abort(404);
    }
}
