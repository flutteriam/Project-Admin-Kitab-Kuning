<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    public $timestamps = true; //by default timestamp false

    protected $fillable = ['category_id', 'title', 'slugs', 'cover', 'type', 'content', 'description', 'likes', 'comments', 'status'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'status' => 'integer',
        'type' => 'integer'
    ];
}
