<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_body_part',
        'category',
        'difficulty_level',
        'instructions',
        'precautions',
        'image_url',
        'video_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function prescribedExercises(): HasMany
    {
        return $this->hasMany(PrescribedExercise::class);
    }
}
