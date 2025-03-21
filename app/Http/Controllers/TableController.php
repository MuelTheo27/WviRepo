<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Content;
use App\Models\SponsorCategory;
use Illuminate\Support\UriQueryString;
use Symfony\Component\Console\Output\ConsoleOutput;
class TableController extends Controller
{

    public function getTableData(Request $request){
        $search_query = $request->query("searchQuery");
        $category = $request->query("sponsorCategory");
        $sort_option = $request->query("option");
        $fiscal_year = $request->query("fiscalYear");

        $query = Content::with([
            'child:id,child_idn,sponsor_id',
            'child.sponsor:id,name,sponsor_category_id',
            'child.sponsor.category:id,sponsor_category_name'
        ]);

        if($fiscal_year){
            $query->where('fiscal_year', $fiscal_year);
        }
        
        if ($search_query) {
            $query->whereHas('child.sponsor', function ($q) use ($search_query) {
                $q->where('name', 'LIKE', "%$search_query%");
            });
        }
        
        $categoryMapping = [
            "b" => "Mass Sponsor",
            "c" => "Middle Sponsor",
            "d" => "Major Sponsor",
            "e" => "Hardcopy"
        ];
        
        if (isset($category)) {
            $sponsorCategory = $categoryMapping[$category] ?? null;
            if ($sponsorCategory !== null) {
                $query->whereHas('child.sponsor.category', function ($q) use ($sponsorCategory) {
                    $q->where('sponsor_category_name', $sponsorCategory);
                });
            }
        }
        
        if (isset($sort_option)) {
            $query->orderBy('created_at', $sort_option);
        }
        
        $data = $query->get()->map(function ($content) {
            return [
                'id' => $content->id, // Include content ID for reference
                'child_idn' => $content->child->child_idn,
                'sponsor_id' => $content->child->sponsor_id,
                'sponsor_name' => $content->child->sponsor->name,
                'sponsor_category' => $content->child->sponsor->category->sponsor_category_name,
                'fiscal_year' => $content->fiscal_year,
                'content_url' => $content->content_url // Include URL for download
            ];
        });
        
        return response()->json($data);
    
  
    }

}
