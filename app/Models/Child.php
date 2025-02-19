<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $primaryKey = 'child_id';
    protected $fillable = ['child_code', 'sponsor_id'];

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    public function content()
    {
        return $this->hasOne(Content::class, 'child_id');
    }

}
