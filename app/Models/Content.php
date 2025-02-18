<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $primaryKey = 'content_id';
    protected $fillable = ['pdf_link', 'child_id'];

    public function child()
    {  
        return $this->belongsTo(Child::class, 'child_id');
    }

}
