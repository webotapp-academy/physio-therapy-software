@extends('layouts.app')

@section('title', 'Schedule Appointment / Home Visit')

@section('content')
<div class="py-6 max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Schedule Patient Visit</h1>
            <p class="text-xs text-[#6B7869] mt-1">Book an In-Clinic appointment or dispatch a therapist for a Home Visit.</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="text-xs font-bold text-[#5C6B5A] hover:text-brand-800">
            &larr; Back to Schedule
        </a>
    </div>

    <form action="{{ route('appointments.store') }}" method="POST" class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-6 space-y-6">
        @csrf

        <!-- Patient Selection -->
        <div>
            <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Select Patient *</label>
            <select name="patient_id" id="patientSelect" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                <option value="">-- Choose Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" 
                        data-address="{{ $patient->address }}" 
                        data-landmark="{{ $patient->landmark }}"
                        {{ (old('patient_id') == $patient->id || $selectedPatientId == $patient->id) ? 'selected' : '' }}>
                        {{ $patient->full_name }} ({{ $patient->patient_uid }}) - {{ $patient->phone }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Service Location Toggle (Clinic vs Home Visit) -->
        <div>
            <label class="block text-xs font-bold text-[#3C4A3A] mb-2">Service Location *</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer border border-[#E6DFD3] rounded-xl p-3.5 flex items-center gap-3 hover:bg-ivory-100 transition-colors has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/70 has-[:checked]:ring-2 has-[:checked]:ring-brand-500/20">
                    <input type="radio" name="service_location" value="clinic" {{ old('service_location', 'clinic') === 'clinic' ? 'checked' : '' }} onchange="toggleLocationFields('clinic')" class="text-brand-600 focus:ring-brand-500">
                    <div>
                        <div class="text-xs font-bold text-[#1F2C1E] flex items-center gap-1.5">
                            🏥 In-Clinic Appointment
                        </div>
                        <div class="text-[11px] text-[#6B7869]">Patient visits center &bull; Waiting token assigned</div>
                    </div>
                </label>

                <label class="cursor-pointer border border-[#E6DFD3] rounded-xl p-3.5 flex items-center gap-3 hover:bg-ivory-100 transition-colors has-[:checked]:border-brand-700 has-[:checked]:bg-ivory-200/80 has-[:checked]:ring-2 has-[:checked]:ring-brand-700/20">
                    <input type="radio" name="service_location" value="home_visit" {{ old('service_location') === 'home_visit' ? 'checked' : '' }} onchange="toggleLocationFields('home_visit')" class="text-brand-700 focus:ring-brand-500">
                    <div>
                        <div class="text-xs font-bold text-[#2E412C] flex items-center gap-1.5">
                            🚗 Home Service Visit
                        </div>
                        <div class="text-[11px] text-[#5C6B5A]">Therapist travels to patient &bull; Travel fee added</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Conditional Home Visit Details -->
        <div id="homeVisitBox" class="p-4 rounded-xl bg-ivory-200/70 border border-[#E6DFD3] space-y-4 {{ old('service_location') === 'home_visit' ? '' : 'hidden' }}">
            <div class="text-xs font-bold text-[#2E412C] flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                Home Visit Travel &amp; Logistics Configuration
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Patient Home Address</label>
                    <textarea name="home_address" id="homeAddress" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-white" placeholder="Full residential address for therapist navigation">{{ old('home_address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Landmark</label>
                    <input type="text" name="home_landmark" id="homeLandmark" value="{{ old('home_landmark') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-white" placeholder="e.g. Near Metro / Opp. Park">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">One-Way Distance (Kilometers)</label>
                    <input type="number" step="0.5" name="travel_distance_km" id="distanceKm" value="{{ old('travel_distance_km', '5.0') }}" oninput="calculateTravelFee()" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Travel Allowance / Conveyance Fee (₹)</label>
                    <input type="number" step="10" name="travel_charge" id="travelCharge" value="{{ old('travel_charge', '250.00') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-white font-bold text-[#2E412C]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Travel Buffer in Schedule (Minutes)</label>
                    <input type="number" name="travel_buffer_minutes" value="{{ old('travel_buffer_minutes', '30') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-white">
                </div>
            </div>
        </div>

        <!-- Therapist & Service -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Assigned Physiotherapist *</label>
                <select name="therapist_id" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                    <option value="">-- Choose Doctor --</option>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" {{ old('therapist_id') == $therapist->id ? 'selected' : '' }}>
                            {{ $therapist->name }} ({{ $therapist->therapistProfile->specialization ?? 'Physiotherapist' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Service Required</label>
                <select name="service_id" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} (Base: ₹{{ number_format($service->base_rate, 0) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Date & Time -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Date *</label>
                <input type="date" name="appointment_date" required value="{{ old('appointment_date', date('Y-m-d')) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Start Time *</label>
                <input type="time" name="start_time" required value="{{ old('start_time', '10:00') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#3C4A3A] mb-1">End Time *</label>
                <input type="time" name="end_time" required value="{{ old('end_time', '10:45') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Clinical / Special Instructions</label>
            <textarea name="notes" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white" placeholder="e.g. Bring portable IFT unit, post-op dressing check, wheelchair access required...">{{ old('notes') }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6DFD3]">
            <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl border border-[#E6DFD3] text-xs font-bold text-[#4A5749] hover:bg-ivory-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition-all">
                Confirm &amp; Book Appointment
            </button>
        </div>
    </form>

</div>

<script>
    function toggleLocationFields(type) {
        const box = document.getElementById('homeVisitBox');
        if (type === 'home_visit') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    document.getElementById('patientSelect').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const addr = selected.getAttribute('data-address');
        const lnd = selected.getAttribute('data-landmark');
        if (addr) {
            document.getElementById('homeAddress').value = addr;
        }
        if (lnd) {
            document.getElementById('homeLandmark').value = lnd;
        }
    });

    function calculateTravelFee() {
        const km = parseFloat(document.getElementById('distanceKm').value) || 0;
        let fee = 150;
        if (km > 2) {
            fee += (km - 2) * 25;
        }
        document.getElementById('travelCharge').value = fee.toFixed(2);
    }
</script>
@endsection
