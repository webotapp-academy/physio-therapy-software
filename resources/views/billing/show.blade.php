@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_no)

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6">

    <!-- Screen Navigation -->
    <div class="flex items-center justify-between print:hidden">
        <div>
            <div class="text-xs text-slate-500 mb-1">
                <a href="{{ route('billing.index') }}" class="hover:text-brand-600">Invoices</a> &bull;
                <a href="{{ route('patients.show', $invoice->patient_id) }}" class="hover:text-brand-600">{{ $invoice->patient->full_name }}</a>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Invoice &amp; Official Receipt</h1>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-bold bg-brand-800 text-white hover:bg-brand-900 shadow-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Invoice (PDF)
            </button>
            <a href="{{ route('patients.show', $invoice->patient_id) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold border border-ivory-300 text-slate-700 hover:bg-ivory-100 transition-colors">
                Back to Patient
            </a>
        </div>
    </div>

    <!-- Official Printable Invoice Card -->
    <div class="bg-[#FDFCF9] rounded-3xl border border-ivory-300 shadow-md p-8 space-y-8 print:border-none print:shadow-none print:p-0">

        <!-- Clinic Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-6 border-b border-ivory-200 gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-black text-lg">
                        +
                    </div>
                    <span class="text-2xl font-black tracking-tight text-slate-900">
                        Physio<span class="text-brand-600">Care</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">
                    Center for Advanced Physiotherapy, Orthopedic Rehabilitation &amp; Domiciliary Home Care.
                </p>
                <div class="text-[11px] text-slate-400 mt-2 font-medium">
                    Phone: +91 98765 43210 &bull; Email: billing@physiocare.local
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-xs uppercase font-extrabold tracking-widest text-brand-700">Tax Invoice &amp; Cash Receipt</span>
                <div class="text-xl font-mono font-black text-slate-900 mt-1">{{ $invoice->invoice_no }}</div>
                <div class="text-xs text-slate-500 mt-1">Date: <strong>{{ $invoice->invoice_date->format('d M, Y') }}</strong></div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $invoice->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ strtoupper($invoice->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bill To Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-2xl bg-[#FAF7F2] border border-ivory-200 text-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Patient Details (Billed To):</span>
                <div class="text-sm font-bold text-slate-900 mt-1">{{ $invoice->patient->full_name }}</div>
                <div class="text-slate-600 font-mono mt-0.5">UID: {{ $invoice->patient->patient_uid }}</div>
                <div class="text-slate-600 mt-0.5">Phone: {{ $invoice->patient->phone }}</div>
                @if($invoice->patient->address)
                    <div class="text-slate-500 mt-1">{{ $invoice->patient->address }}</div>
                @endif
            </div>

            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Treatment &amp; Billing Model:</span>
                <div class="text-sm font-bold text-slate-900 mt-1 capitalize">
                    {{ str_replace('_', ' ', $invoice->billing_model) }}
                </div>
                @if($invoice->treatmentSession)
                    <div class="text-slate-600 mt-0.5">
                        Session #{{ $invoice->treatmentSession->session_number }} &bull; Location: {{ ucfirst(str_replace('_', ' ', $invoice->treatmentSession->service_location)) }}
                    </div>
                @elseif($invoice->patientPackage)
                    <div class="text-slate-600 mt-0.5">
                        {{ $invoice->patientPackage->package->name }} ({{ $invoice->patientPackage->total_sessions }} sessions)
                    </div>
                @endif
                @if($invoice->notes)
                    <div class="text-slate-500 mt-1 italic">{{ $invoice->notes }}</div>
                @endif
            </div>
        </div>

        <!-- Itemized Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FAF7F2] text-slate-700 font-bold uppercase text-[11px] border-b border-ivory-300">
                    <tr>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-center">Type</th>
                        <th class="px-4 py-3 text-right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ivory-200">
                    <tr>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900">
                                @if($invoice->billing_model === 'bill_per_session')
                                    Physiotherapy Consultation &amp; Treatment Modality Session
                                @else
                                    Prepaid Therapy Package ({{ $invoice->patientPackage->package->name ?? 'Package' }})
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500">Therapist clinical interventions, modalities &amp; manual therapy.</div>
                        </td>
                        <td class="px-4 py-3.5 text-center text-slate-600">Service</td>
                        <td class="px-4 py-3.5 text-right font-bold text-slate-900">₹{{ number_format($invoice->service_amount, 2) }}</td>
                    </tr>

                    @if($invoice->travel_charge > 0)
                        <tr>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-amber-900">Home Service Domiciliary Travel &amp; Conveyance Allowance</div>
                                <div class="text-[11px] text-amber-700">Physiotherapist home transit charge and portable equipment conveyance.</div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-amber-700 font-semibold">Travel</td>
                            <td class="px-4 py-3.5 text-right font-bold text-amber-900">₹{{ number_format($invoice->travel_charge, 2) }}</td>
                        </tr>
                    @endif

                    @if($invoice->consumables_amount > 0)
                        <tr>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">Clinical Consumables &amp; Supports</div>
                                <div class="text-[11px] text-slate-500">Kinesiology tape, electrodes, or dry needles.</div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-slate-600">Consumable</td>
                            <td class="px-4 py-3.5 text-right font-bold text-slate-900">₹{{ number_format($invoice->consumables_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Total Calculation Box -->
        <div class="flex justify-end pt-4 border-t border-ivory-200">
            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold">₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-brand-700">
                        <span>Discount:</span>
                        <span class="font-bold">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-ivory-300">
                    <span>Total Amount:</span>
                    <span class="text-brand-700">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Paid to Date:</span>
                    <span class="font-bold text-emerald-600">₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold {{ $invoice->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }} pt-1 border-t border-ivory-200">
                    <span>Outstanding Balance:</span>
                    <span>₹{{ number_format($invoice->balance_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Transactions History -->
        @if($invoice->payments->isNotEmpty())
            <div class="pt-4 border-t border-ivory-200">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Settlement Receipts &amp; Transaction Details</h4>
                <div class="space-y-2">
                    @foreach($invoice->payments as $payment)
                        <div class="p-3 rounded-xl bg-brand-50/70 border border-brand-200 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-mono font-bold text-brand-900">{{ $payment->payment_no }}</span> &bull;
                                <span class="text-brand-800 font-semibold uppercase">{{ $payment->payment_method }}</span>
                                @if($payment->transaction_reference)
                                    <span class="text-brand-700 font-mono text-[11px]">({{ $payment->transaction_reference }})</span>
                                @endif
                                <div class="text-[10px] text-brand-600 mt-0.5">
                                    Received on {{ $payment->payment_date->format('d M, Y h:i A') }}
                                </div>
                            </div>
                            <span class="text-sm font-black text-brand-800">₹{{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Settle Balance Form (If unpaid) -->
        @if($invoice->balance_amount > 0)
            <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 print:hidden space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-amber-900">Settle Outstanding Due (₹{{ number_format($invoice->balance_amount, 2) }})</h4>
                        <p class="text-[11px] text-amber-800">Record cash, UPI QR scan, or card transaction to mark this invoice settled.</p>
                    </div>
                </div>

                <form action="{{ route('billing.record-payment', $invoice->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Paying Amount (₹)</label>
                        <input type="number" step="10" name="amount" value="{{ $invoice->balance_amount }}" max="{{ $invoice->balance_amount }}" required class="w-full text-xs rounded-xl border border-amber-300 px-3 py-2 bg-white font-bold">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Method</label>
                        <select name="payment_method" required class="w-full text-xs rounded-xl border border-amber-300 px-3 py-2 bg-white">
                            <option value="upi">UPI / GPay / PhonePe</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card POS</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Txn Ref #</label>
                        <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref" class="w-full text-xs rounded-xl border border-amber-300 px-3 py-2 bg-white">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-xs transition-colors">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

</div>
@endsection
