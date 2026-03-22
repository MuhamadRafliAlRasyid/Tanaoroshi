<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
            'data' => $notifications->items(),
        ]);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca'
        ]);
    }
}
