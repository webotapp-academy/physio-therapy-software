@extends('layouts.app')

@section('title', 'Record Treatment Session & Attendance')

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6">

    @php
        $hasActivePackage = $patient && $patient->patientPackages && $patient->patientPackages->isNotEmpty();
        $activePkg = $hasActivePackage ? $patient->patientPackages->first() : null;
        $defaultBilling = (request('billing_type') === 'package_deduction' || $hasActivePackage) ? 'package_deduction' : 'per_session';
    @endphp

    <!-- Top Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs text-[#6B7869] mb-1">
                <a href="{{ route('sessions.index') }}" class="hover:text-brand-700">Sessions</a> &bull;
                <span>Record Treatment Log &amp; Attendance</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Record Treatment Session &amp; Attendance</h1>
        </div>
        <a href="{{ url()->previous() }}" class="text-xs font-bold text-[#4A5749] hover:text-brand-700">
            &larr; Go Back
        </a>
    </div>

    @if($hasActivePackage)
        <!-- Prominent Package Attendance Notice -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-brand-100 to-ivory-200 border-2 border-brand-400 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-brand-700 text-white mb-1">
                    ✓ Prepaid Treatment Package Active
                </span>
                <div class="text-sm font-extrabold text-[#1F2C1E]">{{ $activePkg->package->name }}</div>
                <div class="text-xs text-[#4A5749] mt-0.5">
                    Recording this session will log attendance for <strong>Session #{{ $activePkg->used_sessions + 1 }}</strong> of {{ $activePkg->total_sessions }} (<strong>{{ $activePkg->remaining_sessions }} sessions remaining</strong>). No cash/billing required.
                </div>
            </div>
            <div class="shrink-0 text-center bg-white px-4 py-2 rounded-xl border border-brand-300">
                <div class="text-[10px] uppercase font-bold text-[#6B7869]">Punch Session</div>
                <div class="text-xl font-black text-brand-700">#{{ $activePkg->used_sessions + 1 }}</div>
                <div class="text-[10px] text-[#6B7869]">of {{ $activePkg->total_sessions }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('sessions.store') }}" method="POST" class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-6 space-y-6">
        @csrf

        @if($appointment)
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
        @endif

        <!-- 1. Patient & Session Metadata -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                1. Patient &amp; Doctor Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Patient Selector -->
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Patient *</label>
                    <select name="patient_id" id="patientSelect" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500 font-semibold" onchange="if(this.value) window.location.href = '{{ route('sessions.create') }}?patient_id=' + this.value">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (old('patient_id', $patient->id ?? null) == $p->id) ? 'selected' : '' }}>
                                {{ $p->full_name }} ({{ $p->patient_uid }}) {{ $p->patientPackages->isNotEmpty() ? '📦 [Pkg Active]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Attending Therapist -->
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Attending Doctor / Physiotherapist *</label>
                    <select name="therapist_id" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500 font-semibold">
                        @foreach($therapists as $t)
                            <option value="{{ $t->id }}" {{ (old('therapist_id', $appointment->therapist_id ?? auth()->id()) == $t->id) ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Session Date -->
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Session Date *</label>
                    <input type="date" name="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white font-semibold">
                </div>
            </div>

            <!-- Service Location (Clinic vs Home Visit) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Service Location *</label>
                    <select name="service_location" id="serviceLocation" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white font-semibold" onchange="toggleTravelFee(this.value)">
                        <option value="clinic" {{ old('service_location', $appointment->service_location ?? 'clinic') === 'clinic' ? 'selected' : '' }}>🏥 In-Clinic Treatment</option>
                        <option value="home_visit" {{ old('service_location', $appointment->service_location ?? 'clinic') === 'home_visit' ? 'selected' : '' }}>🚗 Domiciliary Home Visit</option>
                    </select>
                </div>

                <!-- Pain Scale VAS (0 - 10) -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-xs font-bold text-[#3C4A3A]">Current Pain Level (VAS 0–10) *</label>
                        <span id="vasDisplay" class="text-xs font-extrabold text-brand-700 bg-brand-100 px-2 py-0.5 rounded">5 / 10 (Moderate)</span>
                    </div>
                    <input type="range" name="current_pain_vas" id="vasSlider" min="0" max="10" value="{{ old('current_pain_vas', 5) }}" oninput="updateVAS(this.value)" class="w-full accent-brand-600 cursor-pointer">
                    <div class="flex justify-between text-[10px] text-[#8A7D69] mt-0.5 font-medium">
                        <span>0 - No Pain</span>
                        <span>5 - Moderate</span>
                        <span>10 - Severe</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Clinical Findings & Modalities -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                2. Modalities &amp; Treatment Interventions Applied
            </h2>

            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-2">Physiotherapy Modalities Used in Session</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                    @php
                        $modalities = [
                            'IFT (Interferential Therapy)',
                            'Ultrasound Therapy',
                            'TENS Therapy',
                            'Dry Needling',
                            'Manual Therapy / Mobilization',
                            'Cervical / Lumbar Traction',
                            'Cryotherapy / Ice Pack',
                            'Thermotherapy (Hot Pack)',
                            'Kinesiology Taping',
                            'Cupping Therapy',
                            'Therapeutic Exercise',
                            'Gait Training'
                        ];
                    @endphp
                    @foreach($modalities as $mod)
                        <label class="flex items-center gap-2 p-2 rounded-lg border border-[#E6DFD3] hover:bg-ivory-100 cursor-pointer">
                            <input type="checkbox" name="modalities_used[]" value="{{ $mod }}" class="rounded text-brand-600 focus:ring-brand-500">
                            <span class="text-[#2B352A] text-[11px] font-medium">{{ $mod }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Exercises Performed in Session</label>
                    <textarea name="exercises_done" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white" placeholder="e.g. Pelvic bridges (3x10), Quad sets, Wall slides...">{{ old('exercises_done') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Therapist Clinical Notes / Progression</label>
                    <textarea name="therapist_notes" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white" placeholder="e.g. Swelling reduced, ROM improved by 10 deg, patient tolerated needling well...">{{ old('therapist_notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. Billing Mode: Package Deduction vs Bill Per Session -->
        <div class="bg-gradient-to-br from-brand-50 to-ivory-200 p-5 rounded-2xl border border-brand-200">
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-brand-200/60 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                3. Attendance &amp; Billing Mode
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <!-- Option B: Package Deduction (Recommended if package exists) -->
                <label id="optBLabel" class="cursor-pointer border-2 {{ $defaultBilling === 'package_deduction' ? 'border-brand-600 ring-2 ring-brand-400/40 bg-white' : 'border-[#E6DFD3] bg-white/70' }} rounded-xl p-4 shadow-xs relative transition-all">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="billing_type" id="radioPackage" value="package_deduction" {{ $defaultBilling === 'package_deduction' ? 'checked' : '' }} onchange="toggleBillingSection('package_deduction')" class="text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-bold text-[#1F2C1E] flex items-center gap-1.5">
                            <span>Option 1: Deduct from Active Package</span>
                            @if($hasActivePackage)
                                <span class="bg-brand-700 text-white text-[10px] px-2 py-0.5 rounded-full font-extrabold">Active</span>
                            @endif
                        </span>
                    </div>
                    <p class="text-[11px] text-[#6B7869] mt-1 pl-5">
                        Punches 1 session attendance from the patient's prepaid package (Zero extra fees billed).
                    </p>
                </label>

                <!-- Option A: Bill Per Session -->
                <label id="optALabel" class="cursor-pointer border {{ $defaultBilling === 'per_session' ? 'border-2 border-brand-600 ring-2 ring-brand-400/40 bg-white' : 'border-[#E6DFD3] bg-white/70' }} rounded-xl p-4 shadow-xs relative transition-all">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="billing_type" id="radioPerSession" value="per_session" {{ $defaultBilling === 'per_session' ? 'checked' : '' }} onchange="toggleBillingSection('per_session')" class="text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-bold text-[#1F2C1E]">Option 2: Bill Per Session (Pay-As-You-Go)</span>
                    </div>
                    <p class="text-[11px] text-[#6B7869] mt-1 pl-5">
                        Instantly generates a single session invoice (Session fee + Home travel + Consumables).
                    </p>
                </label>
            </div>

            <!-- Detailed inputs for Option B: Package Deduction -->
            <div id="packageFields" class="mt-4 bg-white p-4 rounded-xl border border-brand-300 shadow-xs space-y-3 {{ $defaultBilling === 'package_deduction' ? '' : 'hidden' }}">
                <div class="text-xs font-bold text-[#1F2C1E]">Package to Deduct Attendance From:</div>
                @if($patient && $patient->patientPackages->isNotEmpty())
                    <select name="patient_package_id" class="w-full text-xs rounded-xl border border-brand-300 px-3 py-2 bg-brand-50 font-bold text-brand-900">
                        @foreach($patient->patientPackages as $pkg)
                            <option value="{{ $pkg->id }}">
                                {{ $pkg->package->name }} &bull; Attendance #{{ $pkg->used_sessions + 1 }} of {{ $pkg->total_sessions }} ({{ $pkg->remaining_sessions }} sessions remaining)
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="text-xs text-amber-800 bg-amber-50 p-3 rounded-xl border border-amber-200">
                        Notice: This patient does not currently have an active prepaid package. Switch to <strong>Option 2 (Bill Per Session)</strong>, or sell a package first from the Billing desk.
                    </div>
                @endif
            </div>

            <!-- Detailed inputs for Option A: Bill Per Session -->
            <div id="perSessionFields" class="mt-4 bg-white p-4 rounded-xl border border-brand-200 shadow-xs space-y-3 {{ $defaultBilling === 'per_session' ? '' : 'hidden' }}">
                <div class="text-xs font-bold text-brand-950 flex items-center justify-between">
                    <span>Pay-Per-Session Invoice Calculator</span>
                    <span class="text-[10px] text-brand-800 bg-brand-100 px-2 py-0.5 rounded font-mono font-bold">Auto-Invoice</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-[#3C4A3A] mb-1">Session Fee (₹)</label>
                        <input type="number" step="50" name="session_fee" id="sessionFee" value="{{ old('session_fee', '600.00') }}" oninput="updateTotal()" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 font-bold text-[#1F2C1E]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-700 mb-1">Home Travel Charge (₹)</label>
                        <input type="number" step="25" name="travel_charge" id="sessionTravelCharge" value="{{ old('travel_charge', ($appointment->travel_charge ?? 0)) }}" oninput="updateTotal()" class="w-full text-xs rounded-xl border border-[#D5CBB9] px-3 py-2 bg-ivory-200 font-bold text-[#2E412C]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-[#3C4A3A] mb-1">Consumables / K-Tape (₹)</label>
                        <input type="number" step="10" name="consumables_amount" id="consumablesFee" value="{{ old('consumables_amount', '0.00') }}" oninput="updateTotal()" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 font-bold text-[#1F2C1E]">
                    </div>
                </div>

                <div class="p-3 rounded-lg bg-ivory-100 flex items-center justify-between text-xs font-bold border border-[#E6DFD3]">
                    <span class="text-[#3C4A3A]">Total Single Session Bill:</span>
                    <span class="text-base text-brand-700 font-extrabold" id="totalAmountDisplay">₹600.00</span>
                </div>

                <!-- Collect payment now toggle -->
                <div class="pt-2 border-t border-[#E6DFD3] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <label class="flex items-center gap-2 text-xs font-bold text-brand-800 cursor-pointer">
                        <input type="checkbox" name="collect_payment_now" value="1" checked id="collectNowCheckbox" class="rounded text-brand-600 focus:ring-brand-500">
                        Collect &amp; Settle Payment Now (Issue Paid Receipt)
                    </label>

                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-[#5C6B5A]">Payment Mode:</label>
                        <select name="payment_method" class="text-xs rounded-lg border border-[#E6DFD3] px-2 py-1 bg-white font-semibold">
                            <option value="upi">UPI / GPay / PhonePe QR</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card POS</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6DFD3]">
            <a href="{{ route('sessions.index') }}" class="px-5 py-2.5 rounded-xl border border-[#E6DFD3] text-xs font-bold text-[#4A5749] hover:bg-ivory-200 transition-colors">
                Cancel
            </a>
            <button type="submit" id="submitBtn" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span id="submitBtnText">{{ $defaultBilling === 'package_deduction' ? 'Punch Attendance & Deduct 1 Session from Package' : 'Complete Session & Generate Invoice' }}</span>
            </button>
        </div>
    </form>

</div>

<script>
    function toggleBillingSection(type) {
        const perSession = document.getElementById('perSessionFields');
        const packageF = document.getElementById('packageFields');
        const optALabel = document.getElementById('optALabel');
        const optBLabel = document.getElementById('optBLabel');
        const btnText = document.getElementById('submitBtnText');

        if (type === 'per_session') {
            perSession.classList.remove('hidden');
            packageF.classList.add('hidden');
            optALabel.classList.add('border-2', 'border-brand-600', 'ring-2', 'ring-brand-400/40');
            optBLabel.classList.remove('border-2', 'border-brand-600', 'ring-2', 'ring-brand-400/40');
            btnText.innerText = 'Complete Session & Generate Invoice';
        } else {
            perSession.classList.add('hidden');
            packageF.classList.remove('hidden');
            optBLabel.classList.add('border-2', 'border-brand-600', 'ring-2', 'ring-brand-400/40');
            optALabel.classList.remove('border-2', 'border-brand-600', 'ring-2', 'ring-brand-400/40');
            btnText.innerText = 'Punch Attendance & Deduct 1 Session from Package';
        }
    }

    function updateVAS(val) {
        const vasDisplay = document.getElementById('vasDisplay');
        let text = val + ' / 10';
        if (val == 0) text += ' (No Pain)';
        else if (val <= 3) text += ' (Mild Pain)';
        else if (val <= 6) text += ' (Moderate Pain)';
        else if (val <= 8) text += ' (Severe Pain)';
        else text += ' (Extreme Pain)';
        vasDisplay.innerText = text;
    }

    function toggleTravelFee(val) {
        const input = document.getElementById('sessionTravelCharge');
        if (val === 'clinic') {
            input.value = '0.00';
        } else if (parseFloat(input.value) === 0) {
            input.value = '250.00';
        }
        updateTotal();
    }

    function updateTotal() {
        const fee = parseFloat(document.getElementById('sessionFee').value) || 0;
        const travel = parseFloat(document.getElementById('sessionTravelCharge').value) || 0;
        const consumables = parseFloat(document.getElementById('consumablesFee').value) || 0;
        const total = fee + travel + consumables;
        document.getElementById('totalAmountDisplay').innerText = '₹' + total.toFixed(2);
    }

    updateTotal();
</script>
@endsection
