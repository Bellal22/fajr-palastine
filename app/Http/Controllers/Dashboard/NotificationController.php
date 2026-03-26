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
    public function index(Request $request)
    {
        $query = auth()->user()->notifications();
        
        $tab = $request->get('tab');
        if ($tab == 'people') {
            $query->whereIn('data->type', ['person_updated', 'new_person_registered']);
        } elseif ($tab == 'need_requests') {
            $query->whereIn('data->type', ['need_request_activated', 'need_request_closing']);
        } elseif ($tab == 'stock') {
            $query->where('data->type', 'stock_alert');
        } elseif ($tab == 'supervisors') {
            $query->whereIn('data->type', ['bulk_sync_completed', 'delegate_updated']);
        } elseif ($tab == 'area_responsibles') {
            $query->where('data->type', 'people_approved');
        }
        
        $notifications = $query->latest()->paginate(20);
        
        return view('dashboard.notifications.index', compact('notifications', 'tab'));
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

    /**
     * Mark a notification as read and redirect to its target.
     */
    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        if ($notification->unread()) {
            $notification->markAsRead();
        }
        
        $data = $notification->data;
        $type = $data['type'] ?? '';
        $targetId = $data['id'] ?? null;
        
        \Log::info('Notification Redirect:', ['type' => $type, 'id' => $targetId]);
        
        switch ($type) {
            case 'stock_alert':
                return redirect('/dashboard/items');
            case 'person_updated':
                if ($targetId) return redirect('/dashboard/people/' . $targetId);
                return redirect('/dashboard/people/updated');
            case 'new_person_registered':
                if ($targetId) return redirect('/dashboard/people/' . $targetId);
                break;
            case 'need_request_updated':
                if ($targetId) return redirect('/dashboard/need_requests/' . $targetId);
                break;
            case 'need_request_activated':
            case 'need_request_closing':
                return redirect('/dashboard/need_requests');
        }
        
        return redirect('/dashboard/notifications');
    }
}
