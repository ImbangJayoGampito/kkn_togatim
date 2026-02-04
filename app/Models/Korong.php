<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Korong extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'latitude',
        'longitude',
 
        'total_households',
        'male_population',
        'female_population',
        'area_size_km2',
        'population_data',
        'wali_korong_id',
        'nagari_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_population' => 'integer',
        'total_households' => 'integer',
        'total_korongs' => 'integer', // Note: Check if this field name is correct
        'male_population' => 'integer',
        'female_population' => 'integer',
        'area_size_km2' => 'float',
        'population_data' => 'array',
    ];

    /**
     * Get the wali korong for this korong.
     */
    public function waliKorong(): BelongsTo
    {
        return $this->belongsTo(WaliKorong::class, 'wali_korong_id');
    }

    /**
     * Get the nagari that owns the korong.
     */
    public function nagari(): BelongsTo
    {
        return $this->belongsTo(Nagari::class, 'nagari_id');
    }
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
    public function facilities(): HasMany
    {
        return $this->hasMany(KorongFacility::class);
    }
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
