<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = ['name', 'sponsor_category_id'];

    public function category()
    {
        return $this->belongsTo(SponsorCategory::class, 'sponsor_category_id');
    }
    
    public function children()
    {
        return $this->hasMany(Child::class, 'sponsor_id');
    }

}
