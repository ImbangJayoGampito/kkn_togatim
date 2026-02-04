<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FacilityType;

class KorongFacility extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'korong_facilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'is_active',
        'established_date',
        'capacity',
        'korong_id',
        'facility_manager_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => FacilityType::class,
        'is_active' => 'boolean',
        'established_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'capacity' => 'integer',
    ];

    /**
     * Get the korong that owns the facility.
     */
    public function korong(): BelongsTo
    {
        return $this->belongsTo(Korong::class);
    }

    /**
     * Get the manager of the facility.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'facility_manager_id');
    }
    public function getTypeLabelAttribute(): string
    {
        // Correct way to call the instance method
        return $this->type->label();
    }
}
