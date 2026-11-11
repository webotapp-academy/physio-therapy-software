<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppointmentTest extends TestCase
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

    public function test_can_schedule_home_service_visit(): void
    {
        $patient = Patient::create([
            'patient_uid' => 'PHY-2026-0002',
            'first_name' => 'Sunita',
            'last_name' => 'Rao',
            'gender' => 'female',
            'phone' => '+91 98888 22222',
            'address' => 'Villa 5, Whitefield',
            'landmark' => 'Near Lake',
            'status' => 'active',
        ]);

        $therapist = User::create([
            'name' => 'Dr. Sharma',
            'email' => 'sharma@physio.local',
            'phone' => '+91 97777 33333',
            'role' => 'therapist',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($this->user)->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'therapist_id' => $therapist->id,
            'service_location' => 'home_visit',
            'home_address' => 'Villa 5, Whitefield',
            'home_landmark' => 'Near Lake',
            'travel_distance_km' => 7.5,
            'travel_charge' => 287.50,
            'appointment_date' => date('Y-m-d'),
            'start_time' => '11:00',
            'end_time' => '12:00',
            'travel_buffer_minutes' => 30,
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'service_location' => 'home_visit',
            'travel_distance_km' => 7.5,
            'travel_charge' => 287.50,
        ]);
    }
}
