<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedBook extends Model
{
    protected $table = 'saved_books';
    protected $fillable = ['book_id','uid'];

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    protected $casts = [
        'book_id' => 'integer',
        'uid' => 'integer',
    ];
}
