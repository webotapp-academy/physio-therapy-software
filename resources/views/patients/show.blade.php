@extends('layouts.app')

@section('title', $patient->full_name . ' - Patient EMR')

@section('content')
<div class="py-6 space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B7869] mb-1">
                <a href="{{ route('patients.index') }}" class="hover:text-brand-800 font-medium">Patients</a>
                <span>/</span>
                <span class="font-mono text-brand-700 font-bold">{{ $patient->patient_uid }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight flex items-center gap-3">
                {{ $patient->full_name }}
                <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase tracking-wider {{ $patient->status === 'active' ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-ivory-200 text-[#4A5749]' }}">
                    {{ $patient->status }}
                </span>
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Book Appointment / Home Visit
            </a>
            <a href="{{ route('sessions.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-brand-700 text-white hover:bg-brand-800 shadow-xs flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Record Session &amp; Bill
            </a>
            <a href="{{ route('patients.edit', $patient->id) }}" class="px-3 py-2 rounded-xl text-xs font-bold border border-[#E6DFD3] text-[#4A5749] bg-ivory-100 hover:bg-ivory-200 transition-colors">
                Edit EMR
            </a>
        </div>
    </div>

    <!-- Patient Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Demographics & Location Card -->
        <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center justify-between">
                <span>Patient Profile</span>
                <span class="text-xs font-mono text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200 font-bold">
                    {{ $patient->patient_uid }}
                </span>
            </h2>

            <div class="text-xs space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-[#6B7869] font-medium">Age &amp; Gender:</span>
                    <span class="font-bold text-[#1F2C1E]">{{ $patient->age ?? 'N/A' }} Years / {{ ucfirst($patient->gender) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#6B7869] font-medium">Blood Group:</span>
                    <span class="font-bold text-[#1F2C1E]">{{ $patient->blood_group ?? 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#6B7869] font-medium">Phone (WhatsApp):</span>
                    <span class="font-bold text-brand-800">{{ $patient->phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#6B7869] font-medium">Emergency Contact:</span>
                    <span class="font-bold text-[#1F2C1E]">{{ $patient->emergency_contact_name }} ({{ $patient->emergency_contact_phone }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#6B7869] font-medium">Occupation:</span>
                    <span class="font-bold text-[#1F2C1E]">{{ $patient->occupation ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Home Address (Crucial for Home Service) -->
            <div class="pt-3 border-t border-[#E6DFD3]">
                <div class="text-xs font-bold text-[#3C4A3A] mb-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    Home Service Location:
                </div>
                <p class="text-xs text-[#2B352A] bg-ivory-100 p-2.5 rounded-xl border border-[#E6DFD3]">
                    {{ $patient->address ?? 'No address recorded' }}
                    @if($patient->landmark)
                        <br><span class="text-[11px] text-brand-700 font-semibold">Landmark: {{ $patient->landmark }}</span>
                    @endif
                </p>
            </div>

            <!-- Medical & Surgical Notes -->
            <div class="pt-2">
                <div class="text-xs font-bold text-[#3C4A3A] mb-1">Chief Complaints / Medical History:</div>
                <p class="text-xs text-[#5C6B5A] bg-ivory-100 p-2.5 rounded-xl border border-[#E6DFD3]">
                    {{ $patient->medical_history ?? 'None noted' }}
                </p>
            </div>
            @if($patient->surgical_history)
                <div class="pt-1">
                    <div class="text-xs font-bold text-rose-800 mb-1">Surgical History / Implants:</div>
                    <p class="text-xs text-rose-900 bg-rose-50 p-2.5 rounded-xl border border-rose-200">
                        {{ $patient->surgical_history }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Right 2 Columns: Active Packages & Treatment Sessions -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Active Treatment Package or Pay-Per-Session Status -->
            <div class="bg-gradient-to-br from-brand-50 to-ivory-200 rounded-2xl border border-brand-200 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-brand-600 text-white shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-[#1F2C1E]">Current Therapy Package &amp; Session Balance</h3>
                            <p class="text-[11px] text-[#6B7869]">Track prepaid package sessions or pay-per-session mode</p>
                        </div>
                    </div>

                    <a href="{{ route('billing.create-package', ['patient_id' => $patient->id]) }}" class="text-xs font-bold text-brand-800 bg-white hover:bg-ivory-100 px-3 py-1.5 rounded-xl border border-brand-200 shadow-xs transition-colors">
                        + Assign New Package
                    </a>
                </div>

                @php
                    $activePackage = $patient->patientPackages->where('status', 'active')->first();
                @endphp

                @if($activePackage)
                    <div class="bg-white rounded-xl p-4 border border-brand-200 shadow-xs space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-extrabold text-brand-800">{{ $activePackage->package->name }}</span>
                                <div class="text-[11px] text-[#6B7869]">Valid until: {{ $activePackage->expiry_date->format('d M, Y') }} &bull; Paid: ₹{{ number_format($activePackage->price_paid, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-brand-700">{{ $activePackage->remaining_sessions }}</span>
                                <span class="text-xs text-[#6B7869] block -mt-1">sessions left</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between text-[11px] font-bold text-[#5C6B5A] mb-1">
                                <span>Completed Attendance: {{ $activePackage->used_sessions }} / {{ $activePackage->total_sessions }}</span>
                                <span>{{ round(($activePackage->used_sessions / $activePackage->total_sessions) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-ivory-200 rounded-full h-3 overflow-hidden border border-[#E6DFD3]">
                                <div class="bg-gradient-to-r from-brand-700 to-brand-500 h-3 rounded-full" style="width: {{ ($activePackage->used_sessions / $activePackage->total_sessions) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Visual 10-Session Attendance Punch Grid -->
                        <div class="pt-3 border-t border-brand-200/60">
                            <span class="text-[11px] font-bold text-brand-900 block mb-2 uppercase tracking-wider">
                                Package Attendance Punch Card ({{ $activePackage->used_sessions }} of {{ $activePackage->total_sessions }} Attended):
                            </span>
                            <div class="grid grid-cols-5 sm:grid-cols-10 gap-2">
                                @for($i = 1; $i <= $activePackage->total_sessions; $i++)
                                    @php
                                        $isAttended = $i <= $activePackage->used_sessions;
                                        $isNext = $i === ($activePackage->used_sessions + 1);
                                    @endphp
                                    <div class="text-center p-2 rounded-xl border text-xs {{ $isAttended ? 'bg-brand-600 text-white border-brand-700 shadow-xs' : ($isNext ? 'bg-amber-100 text-amber-900 border-amber-300 ring-2 ring-amber-400/50 font-extrabold animate-pulse' : 'bg-ivory-100 text-[#8A7D69] border-[#E6DFD3]') }}">
                                        <div class="text-[9px] uppercase tracking-wider {{ $isAttended ? 'text-brand-100' : 'text-[#8A7D69]' }}">#{{ $i }}</div>
                                        <div class="font-bold mt-0.5">
                                            @if($isAttended)
                                                ✓ Done
                                            @elseif($isNext)
                                                ★ Next
                                            @else
                                                ○ Left
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Quick Attendance Punch Action -->
                        @if($activePackage->remaining_sessions > 0)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-brand-50 border border-brand-200">
                                <div class="text-xs text-brand-900 font-semibold">
                                    Patient arrived for Session #{{ $activePackage->used_sessions + 1 }}?
                                </div>
                                <a href="{{ route('sessions.create', ['patient_id' => $patient->id, 'billing_type' => 'package_deduction']) }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Punch Attendance (Session #{{ $activePackage->used_sessions + 1 }}) &rarr;
                                </a>
                            </div>
                        @else
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 font-bold text-center">
                                All {{ $activePackage->total_sessions }} sessions in this package have been attended.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white/80 rounded-xl p-4 border border-[#E6DFD3] flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#1F2C1E]">No Active Prepaid Package</span>
                            <p class="text-[11px] text-[#6B7869]">Patient is currently on <strong>Pay Per Session (Pay-As-You-Go)</strong>.</p>
                        </div>
                        <span class="text-xs font-bold text-brand-800 bg-brand-50 px-3 py-1 rounded-full border border-brand-200">
                            Bill Per Session Active
                        </span>
                    </div>
                @endif
            </div>

            <!-- Treatment Sessions History -->
            <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E6DFD3] flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-[#1F2C1E]">Treatment Session Logs (Clinical Progress)</h3>
                        <p class="text-xs text-[#6B7869]">Session modalities, exercises, therapist clinical notes &amp; VAS pain velocity</p>
                    </div>
                    <a href="{{ route('sessions.create', ['patient_id' => $patient->id]) }}" class="text-xs font-bold text-brand-700 hover:text-brand-900">
                        + Add Session
                    </a>
                </div>

                <div class="divide-y divide-[#E6DFD3]/60">
                    @forelse($patient->treatmentSessions->sortByDesc('session_date') as $session)
                        <div class="p-5 hover:bg-ivory-100/60 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-800 font-extrabold flex items-center justify-center text-xs border border-brand-200">
                                        #{{ $session->session_number }}
                                    </span>
                                    <div>
                                        <div class="text-xs font-bold text-[#1F2C1E]">
                                            {{ $session->session_date->format('d M, Y') }} &bull; Therapist: {{ $session->therapist->name }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            @if($session->service_location === 'home_visit')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-ivory-200 text-[#4A5749] border border-[#D5CBB9]">
                                                    🚗 Home Visit
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-100 text-brand-800 border border-brand-200">
                                                    🏥 Clinic
                                                </span>
                                            @endif

                                            @if($session->billing_type === 'per_session')
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-50 text-brand-800 border border-brand-200">
                                                    Bill Per Session ({{ ucfirst($session->payment_status) }})
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-100 text-brand-800">
                                                    Package Deducted
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- VAS Pain Badge -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-[#6B7869] font-semibold">Pain (VAS):</span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $session->current_pain_vas <= 3 ? 'bg-brand-100 text-brand-800' : ($session->current_pain_vas <= 6 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $session->current_pain_vas }} / 10
                                    </span>
                                </div>
                            </div>

                            <!-- Modalities & Notes -->
                            <div class="mt-3 text-xs text-[#5C6B5A] bg-ivory-100 p-3 rounded-xl border border-[#E6DFD3] space-y-1">
                                <div><strong>Modalities Applied:</strong> {{ is_array($session->modalities_used) ? implode(', ', $session->modalities_used) : $session->modalities_used }}</div>
                                @if($session->exercises_done)
                                    <div><strong>Exercises Done:</strong> {{ $session->exercises_done }}</div>
                                @endif
                                @if($session->therapist_notes)
                                    <div><strong>Clinical Notes:</strong> {{ $session->therapist_notes }}</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-[#8A7D69] text-xs">
                            No treatment sessions recorded yet. Click "+ Add Session" to log the first visit.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Invoices & Receipts -->
            <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E6DFD3] flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-[#1F2C1E]">Billing History &amp; Invoices</h3>
                        <p class="text-xs text-[#6B7869]">Itemized bills for sessions, home travel, and packages</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-ivory-100 text-[#5C6B5A] font-bold uppercase border-b border-[#E6DFD3] text-[10px]">
                            <tr>
                                <th class="px-4 py-3">Invoice #</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Total Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">View / Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E6DFD3]/60">
                            @forelse($patient->invoices as $inv)
                                <tr class="hover:bg-ivory-100/50">
                                    <td class="px-4 py-3 font-mono font-bold text-brand-800">{{ $inv->invoice_no }}</td>
                                    <td class="px-4 py-3 text-[#5C6B5A]">{{ $inv->invoice_date->format('d M, Y') }}</td>
                                    <td class="px-4 py-3 text-[#3C4A3A] capitalize">{{ str_replace('_', ' ', $inv->billing_model) }}</td>
                                    <td class="px-4 py-3 font-extrabold text-[#1F2C1E]">₹{{ number_format($inv->total_amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $inv->payment_status === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($inv->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('billing.show', $inv->id) }}" class="font-bold text-brand-700 hover:text-brand-900">
                                            Invoice &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-[#8A7D69]">No invoices issued for this patient yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
