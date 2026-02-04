<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nagari extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'description', 'wali_nagari_id'
    ];

    public function waliNagari(): BelongsTo
    {
        return $this->belongsTo(WaliNagari::class, 'wali_nagari_id');
    }

    public function korongs(): HasMany
    {
        return $this->hasMany(Korong::class, 'nagari_id');
    }
    
}
