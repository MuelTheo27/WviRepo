<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\SponsorCategory;

class TableController extends Controller
{
    public function getTableData()
    {
        // Fetch all sponsors with their category and children
        $sponsors = Sponsor::with(['category', 'children'])->get();
        return response()->json($sponsors);
    }

    public function getTablePage()
    {
        // Fetch all sponsors with their category and children
        $sponsors = Sponsor::with(['category', 'children'])->get();
        return view('table', compact('sponsors'));
    }

    public function searchSponsor(Request $request)
    {
        $query = $request->query('query'); // Get the search query from the URL parameter
        dd($query);
        // Search by sponsor_name, sponsor_id, or sponsor_category_name
        $sponsors = Sponsor::where('sponsor_name', 'like', '%' . $query . '%')
            ->orWhere('sponsor_id', 'like', '%' . $query . '%')
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('sponsor_category_name', 'like', '%' . $query . '%');
            })
            ->with(['category', 'children']) // Include category and children in the response
            ->get();

        
        return response()->json($sponsors);
    }

    public function sortSponsor(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort by newest (created_at)
        $order = $request->query('order', 'desc'); // Default order is descending

        // Validate sort_by to prevent SQL injection
        $validSortColumns = ['sponsor_id', 'sponsor_name', 'created_at'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
        }

        // Sort sponsors and include category and children in the response
        $sponsors = Sponsor::orderBy($sortBy, $order)
            ->with(['category', 'children'])
            ->get();

        return response()->json($sponsors);
    }

    public function addSponsor(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'sponsor_name' => 'required|string|max:255',
            'sponsor_category_id' => 'required|exists:sponsor_categories,sponsor_category_id',
        ]);

        // Create a new sponsor
        $sponsor = Sponsor::create($validatedData);

        // Return the newly created sponsor with a 201 status
        return response()->json($sponsor, 201);
    }

    public function deleteSponsor($id)
    {
        // Find the sponsor by ID or fail
        $sponsor = Sponsor::findOrFail($id);

        // Delete the sponsor
        $sponsor->delete();

        // Return a 204 No Content response
        return response()->json(null, 204);
    }
}
