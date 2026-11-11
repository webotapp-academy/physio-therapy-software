@extends('layouts.app')

@section('title', 'Appointments & Home Visits')

@section('content')
<div class="py-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Appointments &amp; Home Visits</h1>
            <p class="text-xs text-[#6B7869] mt-1">Schedule and manage In-Clinic patient consultations and mobile home therapy visits.</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Schedule New Visit
        </a>
    </div>

    <!-- Filters: Date, Location (Clinic vs Home), Therapist, Status -->
    <div class="bg-[#FDFCF9] p-4 rounded-2xl border border-[#E6DFD3] shadow-xs">
        <form action="{{ route('appointments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase text-[#6B7869] mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-[#6B7869] mb-1">Service Location</label>
                <select name="location" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="">All Locations</option>
                    <option value="clinic" {{ request('location') === 'clinic' ? 'selected' : '' }}>🏥 In-Clinic Visits</option>
                    <option value="home_visit" {{ request('location') === 'home_visit' ? 'selected' : '' }}>🚗 Home Service (Domiciliary)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-[#6B7869] mb-1">Physiotherapist</label>
                <select name="therapist_id" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3 py-2 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="">All Therapists</option>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" {{ request('therapist_id') == $therapist->id ? 'selected' : '' }}>
                            {{ $therapist->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-colors shadow-xs">
                    Filter Visits
                </button>
                @if(request()->hasAny(['date', 'location', 'therapist_id', 'status']))
                    <a href="{{ route('appointments.index') }}" class="bg-ivory-200 hover:bg-ivory-300 text-[#3C4A3A] text-xs font-semibold py-2 px-3 rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-ivory-100 text-[#5C6B5A] font-bold uppercase border-b border-[#E6DFD3] text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Apt # &amp; Time</th>
                        <th class="px-4 py-3.5">Patient Details</th>
                        <th class="px-4 py-3.5">Location &amp; Travel Fee</th>
                        <th class="px-4 py-3.5">Assigned Therapist</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6DFD3]/60">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-ivory-100/60 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-mono font-bold text-brand-800">{{ $apt->appointment_no }}</div>
                                <div class="font-extrabold text-[#1F2C1E] text-sm mt-0.5">
                                    {{ \Carbon\Carbon::parse($apt->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apt->end_time)->format('h:i A') }}
                                </div>
                                <div class="text-[10px] text-[#6B7869]">{{ $apt->appointment_date->format('d M, Y') }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-[#1F2C1E] text-sm hover:text-brand-700">
                                    {{ $apt->patient->full_name }}
                                </a>
                                <div class="text-[11px] text-[#6B7869] font-mono">{{ $apt->patient->patient_uid }} &bull; {{ $apt->patient->phone }}</div>
                            </td>

                            <td class="px-4 py-4">
                                @if($apt->service_location === 'home_visit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-ivory-200 text-[#3C4A3A] border border-[#D5CBB9]">
                                        🚗 Home Service ({{ $apt->travel_distance_km }} km)
                                    </span>
                                    <div class="text-[11px] text-[#2B352A] font-medium mt-1 truncate max-w-xs">
                                        {{ $apt->home_address }}
                                    </div>
                                    <div class="text-[10px] text-[#6B7869]">
                                        Travel Allowance: <strong class="text-[#1F2C1E]">₹{{ number_format($apt->travel_charge, 2) }}</strong> &bull; Buffer: {{ $apt->travel_buffer_minutes }} mins
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-800 border border-brand-200">
                                        🏥 In-Clinic
                                    </span>
                                    @if($apt->token_number)
                                        <div class="text-[11px] font-bold text-[#3C4A3A] mt-1">Waiting Token #{{ $apt->token_number }}</div>
                                    @endif
                                @endif
                            </td>

                            <td class="px-4 py-4 text-[#3C4A3A]">
                                <div class="font-bold">{{ $apt->therapist->name }}</div>
                                <div class="text-[11px] text-[#6B7869]">{{ $apt->service->name ?? 'Physiotherapy' }}</div>
                            </td>

                            <td class="px-4 py-4">
                                @if($apt->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-brand-100 text-brand-800 border border-brand-200">Completed</span>
                                @elseif($apt->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">In Treatment</span>
                                @elseif($apt->status === 'checked_in')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700">Checked In</span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Cancelled</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-ivory-200 text-[#4A5749]">Scheduled</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right space-y-1">
                                @if($apt->status !== 'completed')
                                    <a href="{{ route('sessions.create', ['patient_id' => $apt->patient_id, 'appointment_id' => $apt->id]) }}" class="inline-block px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-xs transition-colors">
                                        Record Session &rarr;
                                    </a>
                                @else
                                    <span class="text-xs text-brand-700 font-bold">Session Logged</span>
                                @endif

                                <!-- Quick Status Switch -->
                                @if($apt->status === 'scheduled')
                                    <form action="{{ route('appointments.update-status', $apt->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="checked_in">
                                        <button type="submit" class="text-[10px] text-[#6B7869] hover:text-[#1F2C1E] underline ml-2">
                                            Mark Checked-In
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8A7D69]">
                                No appointments found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="p-4 border-t border-[#E6DFD3]">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
