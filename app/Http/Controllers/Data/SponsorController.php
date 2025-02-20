<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::all();
        return response()->json($sponsors);
    }

    public function store(Request $request)
    {
        $sponsor = Sponsor::create($request->all());
        return response()->json($sponsor, 201);
    }

    public function show($id)
    {
        $sponsor = Sponsor::findOrFail($id);
        return response()->json($sponsor);
    }

    public function update(Request $request, $id)
    {
        $sponsor = Sponsor::findOrFail($id);
        $sponsor->update($request->all());
        return response()->json($sponsor);
    }

    public function destroy($id)
    {
        Sponsor::destroy($id);
        return response()->json(null, 204);
    }
}
