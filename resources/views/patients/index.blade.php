@extends('layouts.app')

@section('title', 'Patients EMR')

@section('content')
<div class="py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Patient Directory &amp; EMR</h1>
            <p class="text-xs text-[#6B7869] mt-1">Search, manage patient profiles, active treatment packages, and medical histories.</p>
        </div>
        <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Register New Patient
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-[#FDFCF9] p-4 rounded-2xl border border-[#E6DFD3] shadow-xs">
        <form action="{{ route('patients.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase text-[#6B7869] mb-1">Search by Name / Phone / UID</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Amit Verma, 98450..., PHY-2026..." class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-[#6B7869] mb-1">Status</label>
                <select name="status" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 focus:outline-hidden focus:ring-2 focus:ring-brand-500 bg-ivory-100 focus:bg-white transition-colors">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Patients</option>
                    <option value="discharged" {{ request('status') === 'discharged' ? 'selected' : '' }}>Discharged</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-colors shadow-xs">
                    Filter Directory
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('patients.index') }}" class="bg-ivory-200 hover:bg-ivory-300 text-[#3C4A3A] text-xs font-semibold py-2 px-3 rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-ivory-100 text-[#5C6B5A] font-bold uppercase border-b border-[#E6DFD3] text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Patient Details</th>
                        <th class="px-4 py-3.5">Contact &amp; Location</th>
                        <th class="px-4 py-3.5">Active Package / Sessions</th>
                        <th class="px-4 py-3.5">Condition / Diagnosis</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6DFD3]/60">
                    @forelse($patients as $patient)
                        @php
                            $activePkg = $patient->patientPackages->where('status', 'active')->first();
                        @endphp
                        <tr class="hover:bg-ivory-100/60 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('patients.show', $patient->id) }}" class="font-bold text-[#1F2C1E] text-sm hover:text-brand-700">
                                    {{ $patient->full_name }}
                                </a>
                                <div class="text-[11px] text-brand-700 font-mono font-medium mt-0.5">
                                    {{ $patient->patient_uid }} &bull; {{ $patient->age }} yrs &bull; {{ ucfirst($patient->gender) }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-[#5C6B5A]">
                                <div class="font-semibold text-[#1F2C1E]">{{ $patient->phone }}</div>
                                @if($patient->address)
                                    <div class="text-[11px] text-[#6B7869] truncate max-w-xs flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 text-[#8A7D69] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $patient->address }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($activePkg)
                                    <div class="font-bold text-brand-800 text-[11px]">{{ $activePkg->package->name ?? 'Package' }}</div>
                                    <div class="w-36 bg-ivory-200 rounded-full h-2 mt-1 overflow-hidden border border-[#E6DFD3]">
                                        <div class="bg-brand-600 h-2 rounded-full" style="width: {{ ($activePkg->used_sessions / $activePkg->total_sessions) * 100 }}%"></div>
                                    </div>
                                    <div class="text-[10px] text-[#6B7869] mt-0.5 font-semibold">
                                        {{ $activePkg->used_sessions }} / {{ $activePkg->total_sessions }} completed ({{ $activePkg->remaining_sessions }} left)
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-ivory-200 text-[#4A5749] border border-[#E6DFD3]">
                                        Pay Per Session
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-[#5C6B5A] max-w-xs truncate">
                                {{ $patient->medical_history ?? 'No notes' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $patient->status === 'active' ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-ivory-200 text-[#4A5749]' }}">
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold text-brand-800 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition-colors">
                                    View EMR &rarr;
                                </a>
                                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg font-bold text-[#3C4A3A] bg-ivory-200 hover:bg-ivory-300 border border-[#E6DFD3] transition-colors">
                                    + Visit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8A7D69]">
                                No patients found. Click "+ Register New Patient" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="p-4 border-t border-[#E6DFD3]">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
