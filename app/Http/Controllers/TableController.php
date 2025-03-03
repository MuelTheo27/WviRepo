<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Support\UriQueryString;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableController extends Controller
{


    public function getTableData(Request $request){

        $search_query = $request->query("search_query");
        $category = $request->query("category");
        $sort_option = $request->query("option");

        $data = Child::select('children.id', 'children.child_code', 'children.sponsor_id')
        ->with([
            'sponsor:id,name,sponsor_category_id', 
            'sponsor.category:id,sponsor_category_name',
            'content:id,child_id,fiscal_year'
        ]);

        if(isset($search_query)){
            $data = $data->whereHas('sponsor', function ($q) use ($search_query) {
                $q->where('name', 'LIKE', "%$search_query%");
            });
        }

        $categoryMapping = [
            1=> "Mass Sponsor",
            2=> "Middle Sponsor",
            3=> "Major Sponsor"
        ];

        if(isset($category)){
            $sponsorCategory = $categoryMapping[$category] ?? null;
            
            $data =  $data->whereHas('sponsor.category', function ($query) use ($sponsorCategory) {
                $query->where('sponsor_category_name', $sponsorCategory); 
            });
        }

        if(isset($sort_option)){
            $data = $data->orderBy('created_at', $sort_option);
        }

        $data = $data->get()
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
    
    public function deleteData(Request $request)
    {
        $child_code = $request->query("child_code");
    
        $deleted = Child::where("child_code", $child_code)->delete();
    
        if ($deleted) {
            return response()->json(["status" => "success"], 200);
        }
    
        return response()->json([
            "status" => "failed",
            "message" => "Record not found or already deleted"
        ], 404);
    }
    

}
