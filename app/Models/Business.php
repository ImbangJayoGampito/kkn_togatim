<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\BusinessType;

class Business extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'type',
        'longitude',
        'latitude',
        'user_id',
        'korong_id'
    ];
    protected $casts = [
        'type' => BusinessType::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(ProductTransaction::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function employeeRoles(): HasMany
    {
        return $this->hasMany(EmployeeRole::class);
    }
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function getTypeLabelAttribute(): string
    {
        // Correct way to call the instance method
        return $this->type->label();
    }
    public function korong(): BelongsTo
    {
        return $this->belongsTo(Korong::class, "korong_id");
    }
}
