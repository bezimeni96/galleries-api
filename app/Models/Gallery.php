<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'description',
        'author_id'
    ];

    public function author() {
        return $this->belongsTo('App\Models\User');
    }
}
