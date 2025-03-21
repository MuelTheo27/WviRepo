<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
class DeleteController extends Controller
{
    //
    public function handleDeletion(Request $request){
        $deleteList = (array) $request->json("deleteList");
        try {
            \DB::transaction(function() use ($deleteList){
                foreach($deleteList as $item){
                    $child = Child::where("child_idn", $item["child_idn"])->first();

                    if($child){
                        $child->content()->where("fiscal_year", $item['fiscal_year'])->delete();
                        if($child->content()->count() === 0){
                            $child->delete();
                        }
                    }
                }
            });
        } catch (\Throwable $th) {
            return response()->json(['message' => $deleteList], 500);
        }
        
        return response()->json(['message' => 'Deleted'], 200);
    }
}
