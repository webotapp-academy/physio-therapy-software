<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_plan_id',
        'patient_id',
        'therapist_id',
        'appointment_id',
        'session_number',
        'session_date',
        'service_location',
        'modalities_used',
        'exercises_done',
        'therapist_notes',
        'current_pain_vas',
        'patient_feedback',
        'billing_type',
        'patient_package_id',
        'invoice_id',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'modalities_used' => 'array',
            'current_pain_vas' => 'integer',
            'session_number' => 'integer',
        ];
    }

    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
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

    public function patientPackage(): BelongsTo
    {
        return $this->belongsTo(PatientPackage::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
