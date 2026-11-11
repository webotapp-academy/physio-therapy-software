<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_uid',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'age',
        'blood_group',
        'phone',
        'alternate_phone',
        'email',
        'occupation',
        'address',
        'landmark',
        'city',
        'latitude',
        'longitude',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_history',
        'surgical_history',
        'allergies',
        'referral_source',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class);
    }

    public function treatmentSessions(): HasMany
    {
        return $this->hasMany(TreatmentSession::class);
    }

    public function prescribedExercises(): HasMany
    {
        return $this->hasMany(PrescribedExercise::class);
    }

    public function patientPackages(): HasMany
    {
        return $this->hasMany(PatientPackage::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
