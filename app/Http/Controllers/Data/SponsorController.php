<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::all();
        return $sponsors;
    }

    public static function storeSponsor(string $sponsorName, int $sponsorCategory)
    {
        $sponsor = Sponsor::where("sponsor_name", $sponsorName)->first();

        if ($sponsor) {
            return $sponsor->id; 
        }
        return Sponsor::insertGetId([
            'sponsor_name' => $sponsorName,
            'sponsor_category' => $sponsorCategory
        ]);
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
