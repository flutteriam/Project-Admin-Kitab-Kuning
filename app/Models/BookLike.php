<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookLike extends Model
{
    protected $table = 'books_likes';
    protected $fillable = ['book_id','uid'];

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    protected $casts = [
        'book_id' => 'integer',
        'uid' => 'integer',
    ];
}
