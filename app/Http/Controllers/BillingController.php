<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\Patient;
use App\Models\PatientPackage;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['patient', 'payments'])
            ->latest('invoice_date')
            ->latest('id');

        if ($request->filled('model')) {
            $query->where('billing_model', $request->input('model'));
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total_invoiced' => Invoice::sum('total_amount'),
            'total_collected' => Payment::sum('amount'),
            'outstanding_dues' => Invoice::where('payment_status', '!=', 'paid')->sum('balance_amount'),
            'per_session_collected' => Invoice::where('billing_model', 'bill_per_session')->where('payment_status', 'paid')->sum('paid_amount'),
            'packages_collected' => Invoice::where('billing_model', 'package_upfront')->where('payment_status', 'paid')->sum('paid_amount'),
            'unpaid_per_sessions_count' => Invoice::where('billing_model', 'bill_per_session')->where('payment_status', '!=', 'paid')->count(),
        ];

        return view('billing.index', compact('invoices', 'stats'));
    }

    public function show(int $id): View
    {
        $invoice = Invoice::with(['patient', 'payments.receiver', 'appointment.therapist', 'treatmentSession.therapist'])
            ->findOrFail($id);

        return view('billing.show', compact('invoice'));
    }

    public function createPackageInvoice(): View
    {
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        $packages = Package::where('is_active', true)->get();

        return view('billing.create-package-invoice', compact('patients', 'packages'));
    }

    public function storePackageInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'package_id' => 'required|exists:packages,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,upi,card,bank_transfer',
            'transaction_reference' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $package = Package::findOrFail($validated['package_id']);
            $patient = Patient::findOrFail($validated['patient_id']);

            $discount = (float) ($validated['discount_amount'] ?? 0.00);
            $total = max(0, $package->price - $discount);

            // Create patient package record
            $patientPackage = PatientPackage::create([
                'patient_id' => $patient->id,
                'package_id' => $package->id,
                'total_sessions' => $package->total_sessions,
                'used_sessions' => 0,
                'purchase_date' => Carbon::today(),
                'expiry_date' => Carbon::today()->addDays($package->validity_days),
                'price_paid' => $total,
                'status' => 'active',
            ]);

            // Create invoice
            $count = Invoice::whereDate('invoice_date', Carbon::today())->count() + 1;
            $invoiceNo = sprintf('INV-%s-%03d', Carbon::today()->format('Ymd'), $count);

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'patient_id' => $patient->id,
                'billing_model' => 'package_upfront',
                'patient_package_id' => $patientPackage->id,
                'service_amount' => $package->price,
                'subtotal' => $package->price,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'paid_amount' => $total,
                'balance_amount' => 0.00,
                'payment_status' => 'paid',
                'invoice_date' => Carbon::today(),
                'notes' => "Purchased {$package->name} ({$package->total_sessions} sessions)",
            ]);

            // Create payment receipt
            $payCount = DB::table('payments')->whereDate('payment_date', Carbon::today())->count() + 1;
            $payNo = sprintf('PAY-%s-%03d', Carbon::today()->format('Ymd'), $payCount);

            Payment::create([
                'payment_no' => $payNo,
                'invoice_id' => $invoice->id,
                'patient_id' => $patient->id,
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'payment_date' => Carbon::now(),
                'notes' => "Upfront package payment for {$package->name}",
            ]);

            return redirect()->route('billing.show', $invoice->id)
                ->with('success', "Package Invoice {$invoice->invoice_no} generated and paid successfully!");
        });
    }

    public function recordPayment(Request $request, int $invoiceId): RedirectResponse
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$invoice->balance_amount,
            'payment_method' => 'required|in:cash,upi,card,bank_transfer',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            $payCount = DB::table('payments')->whereDate('payment_date', Carbon::today())->count() + 1;
            $payNo = sprintf('PAY-%s-%03d', Carbon::today()->format('Ymd'), $payCount);

            $invoice->payments()->create([
                'payment_no' => $payNo,
                'patient_id' => $invoice->patient_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'payment_date' => Carbon::now(),
                'notes' => $validated['notes'] ?? 'Settlement payment',
            ]);

            $newPaid = $invoice->paid_amount + $validated['amount'];
            $newBalance = max(0, $invoice->total_amount - $newPaid);
            $status = $newBalance == 0 ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaid,
                'balance_amount' => $newBalance,
                'payment_status' => $status,
            ]);

            if ($invoice->treatmentSession) {
                $invoice->treatmentSession->update([
                    'payment_status' => $status === 'paid' ? 'paid' : 'unpaid',
                ]);
            }
        });

        return back()->with('success', 'Payment recorded successfully!');
    }
}
