<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otp';

    public $timestamps = true; //by default timestamp false

    protected $fillable = ['otp','key','status','extra_field'];

    protected $hidden = [
        'updated_at', 'created_at',
    ];
    protected $casts = [
        'status' => 'integer',
    ];
}
