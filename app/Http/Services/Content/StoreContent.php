<?php

namespace App\Http\Services\Content;

use App\Models\Content;
class StoreContent{

    public function store(array $data){
        return \DB::transaction(function() use ($data){
            $content = Content::updateOrCreate([
                'child_id' => $data["child_id"]
            ],[
                'content_url' => $data["content_url"],
                'fiscal_year' => $data["fiscal_year"]
            ]);
            return $content;
        });
    }
}