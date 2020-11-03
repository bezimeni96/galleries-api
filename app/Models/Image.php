<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'gallery_id',
        'order_index'
    ];

    public function gallery() {
        return $this->belongsTo('App\Model\Gallery');
    }
}
