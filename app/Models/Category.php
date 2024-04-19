<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'categories';
    protected $fillable = ['name','slugs','cover','order','status'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'status' => 'integer',
    ];
}
