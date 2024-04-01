<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fcm extends Model
{
    protected $table = 'fcm';
    protected $fillable = ['device_id','fcm_token','status'];

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

}
