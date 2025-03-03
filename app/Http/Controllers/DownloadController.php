<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use App\Models\Child;

class DownloadController extends Controller
{
    public function handle(Request $request){
        $child_code = $request->query("child_code");
        $child = Child::where("child_code", $child_code)->with("content")->first();
     
        if(!$child){
            return response()->json(['error' => 'Child code not found !'], 422);
        }
        $file = Http::get($child->content->content_url);

        return Response::make($file->body(), 200, [
            'Content-Type' => $file->header('Content-Type'),
            'Content-Disposition' => 'attachment; filename="Annual_Progress_Report='.$child_code.'"',
        ]);
    }   
}
