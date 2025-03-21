<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "children";
    protected $primaryKey = 'id';
    protected $fillable = ['child_idn', 'sponsor_id'];

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    public function content()
    {
        return $this->hasOne(Content::class, 'child_idn', 'child_idn');
    }

}
