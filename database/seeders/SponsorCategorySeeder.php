<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SponsorCategory;
class SponsorCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SponsorCategory::insert([
            [
                "id" => 1,
                "sponsor_category_name" => "Mass Sponsor"
            ],
            [
                "id" => 2,
                "sponsor_category_name" => "Middle Sponsor"
            ],
            [
                "id" => 3,
                "sponsor_category_name" => "Major Sponsor"
            ]
        ]);
            
    }   
}
