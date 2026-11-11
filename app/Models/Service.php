<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'service_type',
        'description',
        'duration_minutes',
        'base_rate',
        'home_visit_additional_fee',
        'tax_percentage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_rate' => 'decimal:2',
            'home_visit_additional_fee' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
