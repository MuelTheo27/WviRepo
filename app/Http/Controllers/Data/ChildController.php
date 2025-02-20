<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::all();
        return response()->json($children);
    }

    public function store(Request $request)
    {
        $child = Child::create($request->all());
        return response()->json($child, 201);
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
