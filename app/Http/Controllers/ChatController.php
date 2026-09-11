<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\RetailOrder;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Show the chat page for a specific order.
     */
    public function show($orderId)
    {
        $user = Auth::user();
        
        // Find the order and verify access
        $order = RetailOrder::with(['items.product', 'consumer', 'retailer.user'])
            ->where('id', $orderId)
            ->where(function ($query) use ($user) {
                // Consumer can only access their own orders
                $query->where('consumer_id', $user->id)
                    // Retailer can only access orders assigned to them
                    ->orWhereHas('retailer', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->firstOrFail();
        
        // Get all messages for this order
        $messages = Message::where('order_id', $orderId)
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Mark unread messages as read
        Message::where('order_id', $orderId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        // Determine the other party's name and ID
        if ($user->user_type === 'consumer') {
            $otherPartyName = $order->retailer->business_name ?? 'Muuzaji';
            $otherPartyPhone = $order->retailer->user->phone_number ?? null;
            $receiverId = $order->retailer->user_id ?? null;
        } else {
            // Retailer or Wholesaler
            $otherPartyName = $order->consumer->full_name ?? 'Mteja';
            $otherPartyPhone = $order->consumer->phone_number ?? null;
            $receiverId = $order->consumer_id;
        }
        
        return view('chat.index', compact(
            'order', 
            'messages', 
            'otherPartyName', 
            'otherPartyPhone',
            'receiverId'
        ));
    }

    /**
     * Send a chat message.
     */
    public function send(Request $request, $orderId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Find the order with all necessary relationships
        $order = RetailOrder::with(['consumer', 'retailer.user'])
            ->findOrFail($orderId);
        
        // Determine receiver based on user type
        $receiverId = null;
        
        if ($user->user_type === 'consumer') {
            // Consumer sending to retailer
            if ($order->retailer && $order->retailer->user) {
                $receiverId = $order->retailer->user_id;
            }
        } elseif (in_array($user->user_type, ['retailer', 'wholesaler'])) {
            // Retailer/Wholesaler sending to consumer
            $receiverId = $order->consumer_id;
        }
        
        // Log for debugging
        Log::info('Chat message attempt', [
            'order_id' => $orderId,
            'sender_id' => $user->id,
            'sender_type' => $user->user_type,
            'receiver_id' => $receiverId,
            'order_status' => $order->status,
            'has_retailer' => $order->retailer ? 'yes' : 'no',
            'retailer_user_id' => $order->retailer->user_id ?? 'null',
        ]);
        
        if (!$receiverId) {
            Log::error('Chat: Receiver not found', [
                'order_id' => $orderId,
                'sender_id' => $user->id,
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mpokeaji hapatikani. Tafadhali wasiliana na support.'
                ], 400);
            }
            
            return back()->with('error', 'Mpokeaji hapatikani.');
        }
        
        // Create message
        $message = Message::create([
            'order_id' => $orderId,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false,
            'created_at' => now(),
        ]);
        
        Log::info('Chat message sent', [
            'message_id' => $message->id,
            'order_id' => $orderId,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
        ]);
        
        // If request expects JSON, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ujumbe umetumwa!',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('H:i'),
                ]
            ]);
        }
        
        // Otherwise redirect back
        return back()->with('success', 'Ujumbe umetumwa!');
    }

    /**
     * Fetch messages for an order (AJAX).
     */
    public function fetch($orderId)
    {
        $user = Auth::user();
        
        // Verify user has access
        $order = RetailOrder::where('id', $orderId)
            ->where(function ($query) use ($user) {
                $query->where('consumer_id', $user->id)
                      ->orWhereHas('retailer', function ($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->firstOrFail();
        
        // Mark messages as read
        Message::where('order_id', $orderId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        // Get messages
        $messages = Message::where('order_id', $orderId)
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_name' => $msg->sender_id == $user->id ? 'Wewe' : ($msg->sender->full_name ?? 'Mtumiaji'),
                    'is_mine' => $msg->sender_id == $user->id,
                    'created_at' => $msg->created_at->format('H:i'),
                ];
            });
        
        return response()->json(['messages' => $messages]);
    }
}