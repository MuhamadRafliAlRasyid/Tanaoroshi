<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $notifications = Auth::user()->notifications()
                ->latest()
                ->paginate(20);

            return response()->json([
                'status' => true,
                'unread_count' => Auth::user()->unreadNotifications()->count(),
                'data' => $notifications->items(),
            ]);
        } catch (\Exception $e) {
            Log::error('Notification Index Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil notifikasi'
            ], 500);
        }
    }

    public function markAllRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();

            return response()->json([
                'status' => true,
                'message' => 'Semua notifikasi telah ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Mark All Read Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menandai notifikasi'
            ], 500);
        }
    }
}
