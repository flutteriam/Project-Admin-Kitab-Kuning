<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name','slugs','cover','order','status'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'status' => 'integer',
    ];
}
