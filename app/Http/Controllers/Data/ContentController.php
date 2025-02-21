<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public static function index()
    {
        $content = Content::all();
        return $content;
    }

    public static function store(string $pdfLink)
    {
        $content = Content::where("pdf_link", $pdfLink)->first();

        if ($content) {
            return $content->id; // Return existing sponsor ID
        }

    // Insert new sponsor and return its ID
        return Content::insertGetId([
            "pdf_link" => $pdfLink
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
