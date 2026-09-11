<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;

class FavoriteController extends Controller
{
    public function index()
    {
        return view('consumer.favorites');
    }
    
    public function store()
    {
        return back()->with('success', 'Imeongezwa kwenye vipendwa.');
    }
    
    public function destroy($id)
    {
        return back()->with('success', 'Imeondolewa kwenye vipendwa.');
    }
}