<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the category that owns the Book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
