<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Child;
use Symfony\Component\Console\Output\ConsoleOutput;
class SponsorController extends Controller
{
    public function searchSponsor(Request $request)
    {
        $query = $request->query('query'); // Replace this with the actual search query
$data = Child::select('children.id', 'children.child_code', 'children.sponsor_id')
    ->whereHas('sponsor', function ($q) use ($query) {
        $q->where('name', 'LIKE', "%$query%"); // Use `name`, not `sponsor_name`
    })
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

    public function filterBySponsorCategory(Request $request)
{
    // Map sponsor category names to IDs
    $categoryMapping = [
        1=> "Mass Sponsor",
        2=> "Middle Sponsor",
        3=> "Major Sponsor"
    ];

    // Get sponsor category name from request
    $categoryNumber = $request->query('category');

    // Convert category name to ID (default to null if not found)
    $sponsorCategory = $categoryMapping[$categoryNumber] ?? null;

    // If the category ID is not found, return an empty response
    if ($sponsorCategory === null) {
        return response()->json([], 400); // Bad request if invalid category
    }

    // Query with filter by sponsor_category_id
    $data = Child::select('children.id', 'children.child_code', 'children.sponsor_id')
        ->with([
            'sponsor:id,name,sponsor_category_id', 
            'sponsor.category:id,sponsor_category_name',
            'content:id,child_id,fiscal_year'
        ])
        ->whereHas('sponsor.category', function ($query) use ($sponsorCategory) {
            $query->where('sponsor_category_name', $sponsorCategory);  // Now filtering by ID instead of name
        })
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



}
