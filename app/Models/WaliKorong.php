<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WaliKorong extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id'
    ];

    // One-to-one relationship with User (WaliKorong has one User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // One-to-one relationship with Korong (WaliKorong has one Korong)
    public function korong(): HasOne
    {
        return $this->hasOne(Korong::class, 'wali_korong_id'); // Assuming 'wali_korong_id' is the foreign key in Korong table
    }
}
