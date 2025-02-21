<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sponsor;

class SponsorController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['message' => 'No query provided'], 400);
        }

        // Search sponsors by name, ID, or other attributes
        $sponsors = Sponsor::where('sponsor_name', 'LIKE', "%{$query}%")
                           ->orWhere('sponsor_id', 'LIKE', "%{$query}%")
                           ->orWhere('code', 'LIKE', "%{$query}%")
                           ->get();

        return response()->json(['children' => $sponsors]);
    }

    public function index()
    {
        // Fetch all sponsors from the database
        $children = Sponsor::all();
    
        // Pass the data to the Blade view
        return view('tabledata', compact('children'));
    }
    

}
