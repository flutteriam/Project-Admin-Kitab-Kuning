<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedBab extends Model
{
    protected $table = 'saved_babs';
    protected $fillable = ['bab_id','uid'];

    protected $hidden = [
        'updated_at', 'created_at'
    ];

    protected $casts = [
        'bab_id' => 'integer',
        'uid' => 'integer',
    ];

    /**
     * Get the bab that owns the SavedBab
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class);
    }
}
