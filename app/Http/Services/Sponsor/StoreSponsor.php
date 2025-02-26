<?php
namespace App\Http\Services\Sponsor;
use App\Models\Sponsor;
class StoreSponsor{

    
    public function store(string $name, string $category_id){
        return \DB::transaction(function() use ($name, $category_id){
            $sponsor = Sponsor::updateOrCreate([
                "name" => $name,
            ],
    [
                "sponsor_category_id" => $category_id
            ]);
            return $sponsor;
        });
    }

}