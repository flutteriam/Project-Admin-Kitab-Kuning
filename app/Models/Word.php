<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = "words";
    protected $fillable = [
        'arab',
        'order',
        'book_id',
        'bab_id',
        'chapter_id',
        'translate',
        'basic'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bab()
    {
        return $this->belongsTo(Bab::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
