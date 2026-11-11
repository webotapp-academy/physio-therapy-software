<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_billing_and_sees_revenue_stats_on_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Dr. Sanjeev Roy',
            'email' => 'admin@physio.local',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Physiotherapy Center Dashboard');
        $response->assertSee('Revenue Collected');
        $response->assertSee('Pay-Per-Session Dues');

        $billingResponse = $this->actingAs($admin)->get(route('billing.index'));
        $billingResponse->assertStatus(200);
        $billingResponse->assertSee('Billing');
    }

    public function test_therapist_cannot_access_billing_desk_and_gets_403(): void
    {
        $therapist = User::create([
            'name' => 'Dr. Rajesh Sharma',
            'email' => 'rajesh.physio@physio.local',
            'role' => 'therapist',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($therapist)->get(route('billing.index'));
        $response->assertStatus(403);
    }

    public function test_therapist_dashboard_shows_clinical_metrics_and_hides_revenue(): void
    {
        $therapist = User::create([
            'name' => 'Dr. Rajesh Sharma',
            'email' => 'rajesh.physio@physio.local',
            'role' => 'therapist',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($therapist)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Clinical Workspace');
        $response->assertSee("My Today's Visits", false);
        $response->assertSee('My Completed Sessions');
        $response->assertDontSee('Revenue Collected');
        $response->assertDontSee('Pay-Per-Session Dues');
    }

    public function test_receptionist_can_access_billing_and_sees_desk_collections_on_dashboard(): void
    {
        $receptionist = User::create([
            'name' => 'Kavita Das',
            'email' => 'reception@physio.local',
            'role' => 'receptionist',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($receptionist)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Front Desk');
        $response->assertSee("Today's Collections", false);
        $response->assertSee('Invoices Awaiting Payment');

        $billingResponse = $this->actingAs($receptionist)->get(route('billing.index'));
        $billingResponse->assertStatus(200);
        $billingResponse->assertSee('Billing');
    }

    public function test_sidebar_menus_tailor_to_role(): void
    {
        $therapist = User::create([
            'name' => 'Dr. Rajesh Sharma',
            'email' => 'rajesh.physio@physio.local',
            'role' => 'therapist',
            'password' => Hash::make('password'),
        ]);

        $receptionist = User::create([
            'name' => 'Kavita Das',
            'email' => 'reception@physio.local',
            'role' => 'receptionist',
            'password' => Hash::make('password'),
        ]);

        // Therapist sidebar: shows Clinical Dashboard, Exercise Library; hides Billing & Invoices
        $tResponse = $this->actingAs($therapist)->get(route('dashboard'));
        $tResponse->assertSee('Clinical Dashboard');
        $tResponse->assertSee('Exercise Library');
        $tResponse->assertDontSee('Billing &amp; Invoices', false);

        // Receptionist sidebar: shows Front Desk Dashboard, Billing & Invoices
        $rResponse = $this->actingAs($receptionist)->get(route('dashboard'));
        $rResponse->assertSee('Front Desk Dashboard');
        $rResponse->assertSee('Billing &amp; Invoices', false);
    }
}
