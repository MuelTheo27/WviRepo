<?php
namespace App\Http\Services\Child;
use App\Models\Child;
class StoreChildren{
    public function store(array $data): Child
{
    return \DB::transaction(function () use ($data) {
        return Child::updateOrCreate(
            ['child_code' => $data["child_code"]], // Match on child_code
            ['sponsor_id' => $data["sponsor_id"]]  // Update or set sponsor_id
        );
    });
}

}