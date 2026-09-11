<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Fetch notifications from database
        $notifications = []; // Placeholder
        
        return view('consumer.notifications', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        // Mark notification as read
        return back()->with('success', 'Arifa imesomwa.');
    }
    
    public function markAllAsRead()
    {
        // Mark all as read
        return back()->with('success', 'Arifa zote zimesomwa.');
    }
}