<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use SoftDeletes;
    protected $table = "chapters";
    protected $fillable = [
        'book_id',
        'bab_id',
        'order',
        'translate',
        'description'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bab()
    {
        return $this->belongsTo(Bab::class);
    }

    public function words()
    {
        return $this->hasMany(Word::class);
    }
}
