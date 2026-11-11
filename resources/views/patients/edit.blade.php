@extends('layouts.app')

@section('title', 'Edit ' . $patient->full_name)

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Edit Patient EMR</h1>
            <p class="text-xs text-[#6B7869] mt-1">Update patient demographics, home address, and medical status.</p>
        </div>
        <a href="{{ route('patients.show', $patient->id) }}" class="text-xs font-bold text-[#5C6B5A] hover:text-brand-800">
            &larr; Back to Patient
        </a>
    </div>

    <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Demographics Section -->
        <div>
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                1. Personal Details &amp; Status
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">First Name *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name', $patient->first_name) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Gender *</label>
                    <select name="gender" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                        <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Age (Years)</label>
                    <input type="number" name="age" min="1" max="120" value="{{ old('age', $patient->age) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob', optional($patient->dob)->format('Y-m-d')) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Blood Group</label>
                    <input type="text" name="blood_group" value="{{ old('blood_group', $patient->blood_group) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Patient Status *</label>
                    <select name="status" required class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                        <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="discharged" {{ old('status', $patient->status) === 'discharged' ? 'selected' : '' }}>Discharged</option>
                        <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
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
                    <input type="text" name="phone" required value="{{ old('phone', $patient->phone) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Home Address (For Home Visit Sessions)</label>
                    <textarea name="address" rows="2" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">{{ old('address', $patient->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Landmark</label>
                    <input type="text" name="landmark" value="{{ old('landmark', $patient->landmark) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $patient->city) }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                </div>
            </div>
        </div>

        <!-- 3. Medical Background -->
        <div>
            <h2 class="text-sm font-bold text-[#1F2C1E] pb-2 border-b border-[#E6DFD3] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                3. Clinical Profile
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Medical History / Diagnosis</label>
                    <textarea name="medical_history" rows="3" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">{{ old('medical_history', $patient->medical_history) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Surgical History / Red Flags</label>
                    <textarea name="surgical_history" rows="3" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">{{ old('surgical_history', $patient->surgical_history) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E6DFD3]">
            <a href="{{ route('patients.show', $patient->id) }}" class="px-5 py-2.5 rounded-xl border border-[#E6DFD3] text-xs font-bold text-[#4A5749] hover:bg-ivory-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition-all">
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
