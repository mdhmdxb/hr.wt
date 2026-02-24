<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('core::notifications.index', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $notification = Auth::user()->unreadNotifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            Auth::user()->unreadNotifications->markAsRead();
        }
        return $request->wantsJson() ? response()->json(['ok' => true]) : back();
    }

    public function readAndRedirect(string $id)
    {
        $notification = Auth::user()->unreadNotifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $url = $notification->data['url'] ?? route('dashboard');
            return redirect($url);
        }
        return redirect()->route('dashboard');
    }
}
