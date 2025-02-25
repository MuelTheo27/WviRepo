<?php

namespace App\Http\Controllers\Data;
use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::all();
        return response()->json($children);
    }

    public static function store(string $childCode, string $sponsorId, string $contentId)
    {
        Child::firstOrCreate([
            "child_code" => $childCode,
            "sponsor_id" => $sponsorId,
            "content_id" => $contentId
        ]);
     
    }

    public function show($id)
    {
        $child = Child::findOrFail($id);
        return response()->json($child);
    }

    public function update(Request $request, $id)
    {
        $child = Child::findOrFail($id);
        $child->update($request->all());
        return response()->json($child);
    }

    public function destroy($id)
    {
        Child::destroy($id);
        return response()->json(null, 204);
    }
}
