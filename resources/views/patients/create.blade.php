@extends('layouts.app')

@section('title', 'Register Patient')

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Register New Patient</h1>
            <p class="text-xs text-[#6B7869] mt-1">Create an Electronic Medical Record (EMR) for clinic visits or home therapy sessions.</p>
        </div>
        <a href="{{ route('patients.index') }}" class="text-xs font-bold text-[#5C6B5A] hover:text-brand-800">
            &larr; Back to Patients
        </a>
    </div>

    <form action="{{ route('patients.store') }}" method="POST" class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-6 space-y-6">
        @csrf

        <!-- 1. Demographics Section -->
        <div>
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                1. Personal &amp; Demographic Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Ramesh">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Sharma">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Gender *</label>
                    <select name="gender" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Age (Years)</label>
                    <input type="number" name="age" min="1" max="120" value="{{ old('age') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. 45">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Blood Group</label>
                    <input type="text" name="blood_group" value="{{ old('blood_group') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. O+">
                </div>
            </div>
        </div>

        <!-- 2. Contact & Home Service Location -->
        <div>
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                2. Contact &amp; Home Visit Location Details
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Primary Phone (WhatsApp) *</label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="+91 98765 43210">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Alternate / Emergency Contact</label>
                    <input type="text" name="alternate_phone" value="{{ old('alternate_phone') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="Relation / Phone">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="patient@example.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Desk Job, Athlete, Retired">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Full Home Address (Crucial for Home Service Visits)</label>
                    <textarea name="address" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="Flat No, Building Name, Street / Sector...">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Nearby Landmark</label>
                    <input type="text" name="landmark" value="{{ old('landmark') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Opp. City Metro / Behind Apollo Clinic">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', 'Local City') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>
            </div>
        </div>

        <!-- 3. Clinical Profile & Medical History -->
        <div>
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                3. Clinical Profile &amp; Medical Background
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Chief Complaints / Medical History</label>
                    <textarea name="medical_history" rows="3" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Lumbar disc herniation, post-stroke hemiplegia, frozen shoulder...">{{ old('medical_history') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Surgical History / Implants / Pacemakers (Red Flags)</label>
                    <textarea name="surgical_history" rows="3" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Left Knee Replacement 3 weeks ago, metal plates in ankle...">{{ old('surgical_history') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Allergies</label>
                    <input type="text" name="allergies" value="{{ old('allergies') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Tape adhesives, pain relief gels">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Referral Doctor / Source</label>
                    <input type="text" name="referral_source" value="{{ old('referral_source') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors" placeholder="e.g. Dr. K. Mehta (Ortho), Walk-in, Google">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6DFD3]">
            <a href="{{ route('patients.index') }}" class="px-5 py-2.5 rounded-xl border border-[#E6DFD3] text-xs font-bold text-[#4A5749] hover:bg-ivory-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Complete Registration &amp; Create EMR
            </button>
        </div>
    </form>
</div>
@endsection
