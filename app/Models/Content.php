<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "content";
    protected $primaryKey = 'id';
    protected $fillable = ['content_url', 'child_idn', 'fiscal_year'];

    public function child()
    {  
    return $this->belongsTo(Child::class, 'child_idn', 'child_idn');
    }   


}
