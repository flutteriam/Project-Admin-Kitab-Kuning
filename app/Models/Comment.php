<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['uid','book_id','comments','status'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'status' => 'integer',
    ];
}
