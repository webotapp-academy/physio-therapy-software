<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientPackage;
use App\Models\TreatmentSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TreatmentSessionController extends Controller
{
    public function index(): View
    {
        $sessions = TreatmentSession::with(['patient', 'therapist', 'patientPackage.package', 'appointment'])
            ->latest('session_date')
            ->latest('id')
            ->paginate(15);

        return view('sessions.index', compact('sessions'));
    }

    public function create(Request $request): View
    {
        $patientId = $request->query('patient_id');
        $appointmentId = $request->query('appointment_id');

        $appointment = $appointmentId ? Appointment::with(['patient.patientPackages.package', 'therapist', 'service'])->find($appointmentId) : null;
        $patient = $patientId
            ? Patient::with(['patientPackages' => function ($q) {
                $q->where('status', 'active')->whereColumn('used_sessions', '<', 'total_sessions')->with('package');
            }])->find($patientId)
            : ($appointment ? $appointment->patient : null);

        if ($patient && ! $patient->relationLoaded('patientPackages')) {
            $patient->load(['patientPackages' => function ($q) {
                $q->where('status', 'active')->whereColumn('used_sessions', '<', 'total_sessions')->with('package');
            }]);
        }

        $patients = Patient::where('status', 'active')->with(['patientPackages' => function ($q) {
            $q->where('status', 'active')->whereColumn('used_sessions', '<', 'total_sessions')->with('package');
        }])->orderBy('first_name')->get();

        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();

        return view('sessions.create', compact('patients', 'therapists', 'patient', 'appointment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'therapist_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'session_date' => 'required|date',
            'service_location' => 'required|in:clinic,home_visit',
            'modalities_used' => 'nullable|array',
            'exercises_done' => 'nullable|string',
            'therapist_notes' => 'nullable|string',
            'current_pain_vas' => 'required|integer|min:0|max:10',
            'patient_feedback' => 'nullable|string',
            'billing_type' => 'required|in:per_session,package_deduction',
            'patient_package_id' => 'nullable|exists:patient_packages,id',
            // Per session billing extra inputs
            'session_fee' => 'nullable|numeric|min:0',
            'travel_charge' => 'nullable|numeric|min:0',
            'consumables_amount' => 'nullable|numeric|min:0',
            'collect_payment_now' => 'nullable|boolean',
            'payment_method' => 'nullable|in:cash,upi,card,bank_transfer',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $patient = Patient::findOrFail($validated['patient_id']);
            $sessionCount = TreatmentSession::where('patient_id', $patient->id)->count() + 1;

            $sessionData = [
                'patient_id' => $patient->id,
                'therapist_id' => $validated['therapist_id'],
                'appointment_id' => $validated['appointment_id'] ?? null,
                'session_number' => $sessionCount,
                'session_date' => $validated['session_date'],
                'service_location' => $validated['service_location'],
                'modalities_used' => $validated['modalities_used'] ?? [],
                'exercises_done' => $validated['exercises_done'] ?? null,
                'therapist_notes' => $validated['therapist_notes'] ?? null,
                'current_pain_vas' => $validated['current_pain_vas'],
                'patient_feedback' => $validated['patient_feedback'] ?? null,
                'billing_type' => $validated['billing_type'],
            ];

            // 1. If Package Deduction
            if ($validated['billing_type'] === 'package_deduction' && ! empty($validated['patient_package_id'])) {
                $package = PatientPackage::lockForUpdate()->findOrFail($validated['patient_package_id']);
                $package->increment('used_sessions');
                if ($package->used_sessions >= $package->total_sessions) {
                    $package->update(['status' => 'exhausted']);
                }
                $sessionData['patient_package_id'] = $package->id;
                $sessionData['payment_status'] = 'package_covered';

                $session = TreatmentSession::create($sessionData);
            } else {
                // 2. If BILL PER SESSION
                $sessionFee = (float) ($request->input('session_fee') ?? 600.00);
                $travelCharge = (float) ($request->input('travel_charge') ?? ($validated['service_location'] === 'home_visit' ? 250.00 : 0.00));
                $consumables = (float) ($request->input('consumables_amount') ?? 0.00);
                $total = $sessionFee + $travelCharge + $consumables;

                $collectNow = $request->boolean('collect_payment_now');
                $sessionData['payment_status'] = $collectNow ? 'paid' : 'unpaid';

                $session = TreatmentSession::create($sessionData);

                // Auto-generate Single Session Invoice
                $invoiceCount = Invoice::whereDate('invoice_date', Carbon::today())->count() + 1;
                $invoiceNo = sprintf('INV-%s-%03d', Carbon::today()->format('Ymd'), $invoiceCount);

                $invoice = Invoice::create([
                    'invoice_no' => $invoiceNo,
                    'patient_id' => $patient->id,
                    'billing_model' => 'bill_per_session',
                    'appointment_id' => $validated['appointment_id'] ?? null,
                    'treatment_session_id' => $session->id,
                    'service_amount' => $sessionFee,
                    'travel_charge' => $travelCharge,
                    'consumables_amount' => $consumables,
                    'subtotal' => $total,
                    'total_amount' => $total,
                    'paid_amount' => $collectNow ? $total : 0.00,
                    'balance_amount' => $collectNow ? 0.00 : $total,
                    'payment_status' => $collectNow ? 'paid' : 'unpaid',
                    'invoice_date' => $validated['session_date'],
                    'notes' => 'Per Session Billing for Session #'.$sessionCount.($validated['service_location'] === 'home_visit' ? ' (Home Visit)' : ''),
                ]);

                $session->update(['invoice_id' => $invoice->id]);

                if ($collectNow) {
                    $payCount = DB::table('payments')->whereDate('payment_date', Carbon::today())->count() + 1;
                    $payNo = sprintf('PAY-%s-%03d', Carbon::today()->format('Ymd'), $payCount);

                    $invoice->payments()->create([
                        'payment_no' => $payNo,
                        'patient_id' => $patient->id,
                        'amount' => $total,
                        'payment_method' => $request->input('payment_method', 'cash'),
                        'payment_date' => Carbon::now(),
                        'notes' => 'Instant session payment collected at visit completion',
                    ]);
                }
            }

            // Mark linked appointment as completed
            if (! empty($validated['appointment_id'])) {
                Appointment::where('id', $validated['appointment_id'])->update(['status' => 'completed']);
            }

            return redirect()->route('patients.show', $patient->id)
                ->with('success', "Session #{$session->session_number} recorded successfully under {$session->billing_type}!");
        });
    }

    public function show(int $id): View
    {
        $session = TreatmentSession::with(['patient', 'therapist', 'appointment', 'patientPackage.package', 'invoice.payments'])
            ->findOrFail($id);

        return view('sessions.show', compact('session'));
    }
}
