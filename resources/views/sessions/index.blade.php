@extends('layouts.app')

@section('title', 'Treatment Sessions')

@section('content')
<div class="py-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1F2C1E] tracking-tight">Treatment Sessions &amp; Clinical Progress</h1>
            <p class="text-xs text-[#6B7869] mt-1">Logs of modalities applied, exercises performed, pain assessments, and billing deductions.</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record New Session
        </a>
    </div>

    <!-- Sessions Table -->
    <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-ivory-100 text-[#5C6B5A] font-bold uppercase border-b border-[#E6DFD3] text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Session # &amp; Date</th>
                        <th class="px-4 py-3.5">Patient Details</th>
                        <th class="px-4 py-3.5">Therapist</th>
                        <th class="px-4 py-3.5">Location</th>
                        <th class="px-4 py-3.5">Pain (VAS)</th>
                        <th class="px-4 py-3.5">Billing &amp; Payment</th>
                        <th class="px-5 py-3.5 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E6DFD3]/60">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-ivory-100/60 transition-colors">
                            <td class="px-5 py-4">
                                <span class="w-8 h-8 rounded-lg bg-brand-50 text-brand-800 font-extrabold inline-flex items-center justify-center text-xs border border-brand-200 mr-2">
                                    #{{ $session->session_number }}
                                </span>
                                <span class="font-bold text-[#1F2C1E]">{{ $session->session_date->format('d M, Y') }}</span>
                            </td>

                            <td class="px-4 py-4">
                                <a href="{{ route('patients.show', $session->patient_id) }}" class="font-bold text-[#1F2C1E] text-sm hover:text-brand-700">
                                    {{ $session->patient->full_name }}
                                </a>
                                <div class="text-[11px] text-[#6B7869] font-mono">{{ $session->patient->patient_uid }}</div>
                            </td>

                            <td class="px-4 py-4 font-semibold text-[#3C4A3A]">
                                {{ $session->therapist->name }}
                            </td>

                            <td class="px-4 py-4">
                                @if($session->service_location === 'home_visit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-ivory-200 text-[#3C4A3A] border border-[#D5CBB9]">
                                        🚗 Home Visit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-800 border border-brand-200">
                                        🏥 In-Clinic
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $session->current_pain_vas <= 3 ? 'bg-brand-100 text-brand-800' : ($session->current_pain_vas <= 6 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $session->current_pain_vas }} / 10
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                @if($session->billing_type === 'per_session')
                                    <div class="font-bold text-brand-900 text-xs">Pay Per Session</div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $session->payment_status === 'paid' ? 'bg-brand-100 text-brand-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($session->payment_status) }}
                                    </span>
                                @else
                                    <div class="font-bold text-[#3C4A3A] text-xs">Prepaid Package</div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-ivory-200 text-[#4A5749] border border-[#E6DFD3]">
                                        Package Covered
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" class="font-bold text-brand-700 hover:text-brand-900">
                                    View Full Notes &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#8A7D69]">No sessions logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="p-4 border-t border-[#E6DFD3]">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
