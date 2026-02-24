<?php

namespace Modules\Core\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\ActivityLog;
use Modules\Core\Models\FileBasedOwner;
use Modules\Core\Services\OwnerCredentials;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Prevent cached login form after logout (avoids 419 on next submit)
        return response()
            ->view('core::auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        // File-based owner (no DB trace): check encrypted file first
        if (OwnerCredentials::isOwnerEmail($email) && OwnerCredentials::verify($email, $password)) {
            $data = OwnerCredentials::get();
            $name = $data['name'] ?? 'Owner';
            $request->session()->regenerate();
            $request->session()->put('owner_email', $email);
            $request->session()->put('owner_name', $name);
            Auth::login(new FileBasedOwner($email, $name), $request->boolean('remember'));
            ActivityLog::log('login', 'Owner logged in (file-based)', null);
            return redirect()->intended(route('dashboard'));
        }

        // Normal DB user
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            ActivityLog::log('login', 'User logged in');
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $wasOwner = auth()->check() && auth()->user() instanceof FileBasedOwner;
        ActivityLog::log('logout', $wasOwner ? 'Owner logged out' : 'User logged out', $wasOwner ? null : null);
        Auth::logout();
        $request->session()->forget(['owner_email', 'owner_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
