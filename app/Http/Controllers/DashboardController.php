<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentPlan;
use App\Models\TreatmentSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = Carbon::today();
        $role = $user->role ?? 'admin';

        if ($role === 'therapist') {
            // Therapist-tailored Clinical Dashboard
            $myAppointmentsQuery = Appointment::where('therapist_id', $user->id);
            $mySessionsQuery = TreatmentSession::where('therapist_id', $user->id);

            $stats = [
                'my_today_appointments' => (clone $myAppointmentsQuery)->whereDate('appointment_date', $today)->count(),
                'my_clinic_visits' => (clone $myAppointmentsQuery)->whereDate('appointment_date', $today)->where('service_location', 'clinic')->count(),
                'my_home_visits' => (clone $myAppointmentsQuery)->whereDate('appointment_date', $today)->where('service_location', 'home_visit')->count(),
                'my_completed_sessions' => (clone $mySessionsQuery)->count(),
                'my_active_plans' => TreatmentPlan::where('therapist_id', $user->id)->where('status', 'active')->count(),
                'my_assigned_patients' => (clone $mySessionsQuery)->distinct('patient_id')->count('patient_id'),
            ];

            $todayAppointments = Appointment::with(['patient', 'service'])
                ->where('therapist_id', $user->id)
                ->whereDate('appointment_date', $today)
                ->orderBy('start_time')
                ->get();

            if ($todayAppointments->isEmpty()) {
                $todayAppointments = Appointment::with(['patient', 'service'])
                    ->where('therapist_id', $user->id)
                    ->orderByDesc('appointment_date')
                    ->take(5)
                    ->get();
            }

            $recentSessions = TreatmentSession::with(['patient', 'treatmentPlan'])
                ->where('therapist_id', $user->id)
                ->latest()
                ->take(6)
                ->get();

            $recentInvoices = collect(); // Financial invoices hidden for therapists

            return view('dashboard', compact('stats', 'todayAppointments', 'recentSessions', 'recentInvoices', 'role'));
        }

        if ($role === 'receptionist') {
            // Front-Desk Desk Dashboard
            $stats = [
                'total_patients' => Patient::count(),
                'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
                'today_clinic_appointments' => Appointment::whereDate('appointment_date', $today)->where('service_location', 'clinic')->count(),
                'today_home_visits' => Appointment::whereDate('appointment_date', $today)->where('service_location', 'home_visit')->count(),
                'today_collections' => Payment::whereDate('payment_date', $today)->sum('amount'),
                'pending_invoices_count' => Invoice::where('payment_status', '!=', 'paid')->count(),
                'unpaid_balance_total' => Invoice::where('payment_status', '!=', 'paid')->sum('balance_amount'),
                'active_therapists' => User::where('role', 'therapist')->where('status', 'active')->count(),
            ];

            $todayAppointments = Appointment::with(['patient', 'therapist', 'service'])
                ->whereDate('appointment_date', $today)
                ->orderBy('start_time')
                ->get();

            if ($todayAppointments->isEmpty()) {
                $todayAppointments = Appointment::with(['patient', 'therapist', 'service'])
                    ->orderByDesc('appointment_date')
                    ->take(5)
                    ->get();
            }

            $recentSessions = TreatmentSession::with(['patient', 'therapist'])
                ->latest()
                ->take(6)
                ->get();

            $recentInvoices = Invoice::with(['patient', 'appointment'])
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard', compact('stats', 'todayAppointments', 'recentSessions', 'recentInvoices', 'role'));
        }

        // Admin (Director) - Full Clinic & Financial Overview
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'today_clinic_appointments' => Appointment::whereDate('appointment_date', $today)->where('service_location', 'clinic')->count(),
            'today_home_visits' => Appointment::whereDate('appointment_date', $today)->where('service_location', 'home_visit')->count(),
            'completed_sessions_today' => TreatmentSession::whereDate('session_date', $today)->count(),
            'total_revenue' => Payment::sum('amount'),
            'per_session_revenue' => Invoice::where('billing_model', 'bill_per_session')->where('payment_status', 'paid')->sum('paid_amount'),
            'package_revenue' => Invoice::where('billing_model', 'package_upfront')->where('payment_status', 'paid')->sum('paid_amount'),
            'unpaid_sessions_count' => TreatmentSession::where('billing_type', 'per_session')->where('payment_status', 'unpaid')->count(),
            'active_therapists' => User::where('role', 'therapist')->where('status', 'active')->count(),
        ];

        $todayAppointments = Appointment::with(['patient', 'therapist', 'service'])
            ->whereDate('appointment_date', $today)
            ->orderBy('start_time')
            ->get();

        if ($todayAppointments->isEmpty()) {
            $todayAppointments = Appointment::with(['patient', 'therapist', 'service'])
                ->orderByDesc('appointment_date')
                ->take(5)
                ->get();
        }

        $recentSessions = TreatmentSession::with(['patient', 'therapist', 'treatmentPlan'])
            ->latest()
            ->take(6)
            ->get();

        $recentInvoices = Invoice::with(['patient', 'appointment'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'todayAppointments', 'recentSessions', 'recentInvoices', 'role'));
    }
}
