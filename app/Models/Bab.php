<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bab extends Model
{
    protected $table = "babs";
    protected $fillable = ['book_id', 'order', 'title', 'translate_title'];

    public function book() {
        return $this->belongsTo(Book::class);
    }

    public function chapters() {
        return $this->hasMany(Chapter::class);
    }
}
