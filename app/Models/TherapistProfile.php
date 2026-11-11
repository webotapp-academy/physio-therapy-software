<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qualification',
        'specialization',
        'license_number',
        'experience_years',
        'slot_duration_minutes',
        'consultation_fee',
        'home_visit_fee',
        'is_available_for_home_visits',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'is_available_for_home_visits' => 'boolean',
            'consultation_fee' => 'decimal:2',
            'home_visit_fee' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
