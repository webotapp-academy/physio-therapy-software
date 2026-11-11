<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function therapistProfile(): HasOne
    {
        return $this->hasOne(TherapistProfile::class);
    }

    public function appointmentsAsTherapist(): HasMany
    {
        return $this->hasMany(Appointment::class, 'therapist_id');
    }

    public function treatmentSessionsAsTherapist(): HasMany
    {
        return $this->hasMany(TreatmentSession::class, 'therapist_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTherapist(): bool
    {
        return $this->role === 'therapist';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function canAccessBilling(): bool
    {
        return in_array($this->role, ['admin', 'receptionist'], true);
    }

    public function canAccessClinicalSessions(): bool
    {
        return in_array($this->role, ['admin', 'therapist'], true);
    }

    public function canPrescribeExercises(): bool
    {
        return in_array($this->role, ['admin', 'therapist'], true);
    }
}
