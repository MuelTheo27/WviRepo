<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = "content";
    protected $primaryKey = 'id';
    protected $fillable = ['content_url', 'child_id', 'fiscal_year'];

    public function child()
    {  
        return $this->belongsTo(Child::class, 'child_id');
    }

}
