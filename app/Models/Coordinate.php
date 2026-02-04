<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Coordinate extends Model
{
    protected $fillable = [
        'latitude', 'longitude'
    ];

    public function coordinateable(): MorphTo
    {
        return $this->morphTo();
    }
}
