<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        
        return view('dashboard.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        flash()->success('تم تمييز جميع الإشعارات كمقروءة.');
        
        return redirect()->back();
    }

    /**
     * Get latest notifications for the dropdown.
     */
    public function latest()
    {
        $notifications = Auth::user()->unreadNotifications()->take(5)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
