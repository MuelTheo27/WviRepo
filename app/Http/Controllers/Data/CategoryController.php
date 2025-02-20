<?php


namespace App\Http\Controllers;

use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = SponsorCategory::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $category = SponsorCategory::create($request->all());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = SponsorCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = SponsorCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json($category);
    }

    public function destroy($id)
    {
        SponsorCategory::destroy($id);
        return response()->json(null, 204);
    }
}
