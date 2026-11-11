<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Reception User',
            'email' => 'reception@test.local',
            'role' => 'receptionist',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_can_record_session_with_pay_per_session_billing(): void
    {
        $patient = Patient::create([
            'patient_uid' => 'PHY-2026-0003',
            'first_name' => 'Vikram',
            'last_name' => 'Batra',
            'gender' => 'male',
            'phone' => '+91 96666 44444',
            'status' => 'active',
        ]);

        $therapist = User::create([
            'name' => 'Dr. Sen',
            'email' => 'sen@physio.local',
            'phone' => '+91 95555 55555',
            'role' => 'therapist',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($this->user)->post(route('sessions.store'), [
            'patient_id' => $patient->id,
            'therapist_id' => $therapist->id,
            'session_date' => date('Y-m-d'),
            'service_location' => 'home_visit',
            'modalities_used' => ['TENS Therapy', 'Manual Therapy'],
            'current_pain_vas' => 5,
            'billing_type' => 'per_session',
            'session_fee' => 700.00,
            'travel_charge' => 200.00,
            'consumables_amount' => 50.00,
            'collect_payment_now' => 1,
            'payment_method' => 'upi',
        ]);

        $response->assertRedirect(route('patients.show', $patient->id));

        // Check treatment session created
        $this->assertDatabaseHas('treatment_sessions', [
            'patient_id' => $patient->id,
            'billing_type' => 'per_session',
            'payment_status' => 'paid',
            'service_location' => 'home_visit',
        ]);

        // Check single-session invoice auto-generated
        $this->assertDatabaseHas('invoices', [
            'patient_id' => $patient->id,
            'billing_model' => 'bill_per_session',
            'service_amount' => 700.00,
            'travel_charge' => 200.00,
            'total_amount' => 950.00,
            'payment_status' => 'paid',
        ]);

        // Check payment receipt logged
        $this->assertDatabaseHas('payments', [
            'patient_id' => $patient->id,
            'amount' => 950.00,
            'payment_method' => 'upi',
        ]);
    }
}
