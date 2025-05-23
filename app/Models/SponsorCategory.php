<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorCategory extends Model
{
    use HasFactory;
    
    protected $table = "sponsor_categories";
    protected $primaryKey = 'id';
    protected $fillable = ['sponsor_category_name'];
    
    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'sponsor_category_id');
    }

}
