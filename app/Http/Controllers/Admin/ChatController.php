<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(Request $request, $orderId)
    {
        $request->validate(['message' => 'required|string']);
        // Save message to database
        return back()->with('success', 'Ujumbe umetumwa!');
    }
}