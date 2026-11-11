<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_can_view_patients_directory(): void
    {
        Patient::create([
            'patient_uid' => 'PHY-2026-0001',
            'first_name' => 'Rajesh',
            'last_name' => 'Koothrappali',
            'gender' => 'male',
            'phone' => '+91 98765 00001',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('patients.index'));
        $response->assertStatus(200);
        $response->assertSee('Rajesh Koothrappali');
        $response->assertSee('PHY-2026-0001');
    }

    public function test_can_register_new_patient(): void
    {
        $response = $this->actingAs($this->user)->post(route('patients.store'), [
            'first_name' => 'Kunal',
            'last_name' => 'Nayyar',
            'gender' => 'male',
            'phone' => '+91 99999 11111',
            'age' => 38,
            'address' => 'Flat 101, Indiranagar',
            'landmark' => 'Near Metro',
            'medical_history' => 'Cervical radiculopathy',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'first_name' => 'Kunal',
            'phone' => '+91 99999 11111',
            'landmark' => 'Near Metro',
        ]);
    }
}
