<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'therapist_id',
        'appointment_id',
        'assessment_date',
        'chief_complaint',
        'pain_scale_vas',
        'pain_body_areas',
        'onset_date',
        'history_of_present_illness',
        'posture_gait_assessment',
        'range_of_motion_notes',
        'muscle_power_mmt_notes',
        'special_tests_results',
        'provisional_diagnosis',
        'treatment_goals_short',
        'treatment_goals_long',
        'recommended_sessions_count',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'onset_date' => 'date',
            'pain_body_areas' => 'array',
            'pain_scale_vas' => 'integer',
            'recommended_sessions_count' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function treatmentPlan(): HasOne
    {
        return $this->hasOne(TreatmentPlan::class);
    }
}
