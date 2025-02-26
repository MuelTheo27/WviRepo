<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableController extends Controller
{
    /*
    Function to get all data for the table
    */
    public function getTableData()
    {
        
        $data = Child::select('children.id', 'children.child_code', 'children.sponsor_id')
        ->with([
            'sponsor:id,name,sponsor_category_id', 
            'sponsor.category:id,sponsor_category_name',
            'content:id,child_id,fiscal_year'
        ])
        ->get()
        ->map(function ($child) {
            return [
                'child_code' => $child->child_code,
                'sponsor_name' => $child->sponsor->name,
                'sponsor_category' => $child->sponsor->category->sponsor_category_name,
                'fiscal_year' => $child->content->fiscal_year,
            ];
        });

        return response()->json($data);
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
    public function sortData(Request $request){
        $sortOption = $request->query('order');
    
        if ($sortOption === null) {
            return response()->json([], 400); 
        }
    
        $data = Child::select('children.id', 'children.child_code', 'children.sponsor_id')
        ->with([
            'sponsor:id,name,sponsor_category_id', 
            'sponsor.category:id,sponsor_category_name',
            'content:id,child_id,fiscal_year'
        ])->orderBy('created_at', $sortOption)
        ->get()
        ->map(function ($child) {
            return [
                'child_code' => $child->child_code,
                'sponsor_name' => $child->sponsor->name,
                'sponsor_category' => $child->sponsor->category->sponsor_category_name,
                'fiscal_year' => $child->content->fiscal_year,
            ];
        });

        return response()->json($data);
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
