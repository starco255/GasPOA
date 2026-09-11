<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessProfile;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Idadi ya wauzaji waliopo kwenye mfumo (kwa ajili ya kuonyesha)
        // Business profiles are the authoritative seller records. This also keeps
        // the public home page working with legacy user tables lacking user_type.
        $retailersCount = BusinessProfile::where('business_type', 'retailer')->count();
        $wholesalersCount = BusinessProfile::where('business_type', 'wholesaler')->count();

        return view('home.index', compact('retailersCount', 'wholesalersCount'));
    }
}
