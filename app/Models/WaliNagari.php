<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaliNagari extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id'
    ];

    // One-to-one relationship with User (WaliNagari has one User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // One-to-one relationship with Nagari (WaliNagari has one Nagari)
    public function nagari(): HasOne
    {
        return $this->hasOne(Nagari::class, 'wali_nagari_id'); // Assuming 'wali_nagari_id' is the foreign key in Nagari table
    }
}
