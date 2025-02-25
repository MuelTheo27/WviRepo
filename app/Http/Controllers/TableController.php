<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\SponsorCategory;

class TableController extends Controller
{
    /*
    Function to get all data for the table
    */
    public function getTableData()
    {
        $sponsors = Sponsor::with(['category', 'children'])->get();
        return response()->json($sponsors);
    }

    /*
    Function to view the table page (fetch data from sponsors, categories, and children)
    */
    public function getTablePage()
    {
        // Fetch all sponsors with their category and children
        $sponsors = Sponsor::with(['category', 'children'])->get();
        return view('table', compact('sponsors'));
    }

    /*
    Function for searching sponsors
    */
    public function searchSponsor(Request $request)
    {
        $query = $request->query('query'); 
        dd($query);
        $sponsors = Sponsor::where('sponsor_name', 'like', '%' . $query . '%')
            ->orWhere('sponsor_id', 'like', '%' . $query . '%')
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('sponsor_category_name', 'like', '%' . $query . '%');
            })
            ->with(['category', 'children']) 
            ->get();

        
       

        return response()->json($sponsors);
    }

  
    public function sortSponsor(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at'); 
        $order = $request->query('order', 'desc'); 
        $validSortColumns = ['sponsor_id', 'sponsor_name', 'created_at'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        $sponsors = Sponsor::orderBy($sortBy, $order)
            ->with(['category', 'children'])
            ->get();

        return response()->json($sponsors);
    }

    /*
    Function to add new sponsor data
    */
    public function addSponsor(Request $request)
    {
        $validatedData = $request->validate([
            'sponsor_name' => 'required|string|max:255',
            'sponsor_category_id' => 'required|exists:sponsor_categories,sponsor_category_id',
        ]);

        $sponsor = Sponsor::create($validatedData);
        return response()->json($sponsor, 201);
    }

    /*
    Function to delete a sponsor
    */
    public function deleteSponsor($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->delete();

        return response()->json(null, 204);
    }
}
