<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $orderId)
    {
        return redirect()->route('consumer.history')
                         ->with('success', 'Asante kwa maoni yako!');
    }
    
    public function update(Request $request, $id)
    {
        return back()->with('success', 'Maoni yamesasishwa.');
    }
}