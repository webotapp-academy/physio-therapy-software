@extends('layouts.app')

@section('title', 'Session #' . $session->session_number . ' - ' . $session->patient->full_name)

@section('content')
<div class="py-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs text-slate-500 mb-1">
                <a href="{{ route('sessions.index') }}" class="hover:text-brand-600">Sessions</a> &bull;
                <a href="{{ route('patients.show', $session->patient_id) }}" class="hover:text-brand-600">{{ $session->patient->full_name }}</a>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Treatment Session #{{ $session->session_number }}</h1>
        </div>
        <a href="{{ route('patients.show', $session->patient_id) }}" class="text-xs font-bold text-slate-600 hover:text-brand-600">
            &larr; Back to Patient EMR
        </a>
    </div>

    <!-- Session Detail Card -->
    <div class="bg-[#FDFCF9] rounded-2xl border border-ivory-300 shadow-xs p-6 space-y-6">

        <!-- Top summary bar -->
        <div class="flex flex-wrap items-center justify-between p-4 rounded-xl bg-[#FAF7F2] border border-ivory-200 gap-3">
            <div>
                <span class="text-xs text-slate-500 font-semibold">Date of Session:</span>
                <div class="text-sm font-bold text-slate-900">{{ $session->session_date->format('l, d M, Y') }}</div>
            </div>

            <div>
                <span class="text-xs text-slate-500 font-semibold">Location:</span>
                <div>
                    @if($session->service_location === 'home_visit')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            🚗 Home Service Visit
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-800 border border-brand-200">
                            🏥 In-Clinic Session
                        </span>
                    @endif
                </div>
            </div>

            <div>
                <span class="text-xs text-slate-500 font-semibold">Attending Doctor:</span>
                <div class="text-sm font-bold text-slate-900">{{ $session->therapist->name }}</div>
            </div>

            <div>
                <span class="text-xs text-slate-500 font-semibold">Pain (VAS):</span>
                <div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $session->current_pain_vas <= 3 ? 'bg-emerald-100 text-emerald-800' : ($session->current_pain_vas <= 6 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                        {{ $session->current_pain_vas }} / 10
                    </span>
                </div>
            </div>
        </div>

        <!-- Modalities Applied -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Physiotherapy Modalities &amp; Interventions</h3>
            <div class="flex flex-wrap gap-2">
                @if(is_array($session->modalities_used) && count($session->modalities_used) > 0)
                    @foreach($session->modalities_used as $mod)
                        <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-brand-50 text-brand-800 border border-brand-200">
                            ✓ {{ $mod }}
                        </span>
                    @endforeach
                @else
                    <span class="text-xs text-slate-400">No specific modalities selected.</span>
                @endif
            </div>
        </div>

        <!-- Exercises Done -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Exercises Executed in Session</h3>
            <div class="p-3.5 rounded-xl bg-[#FAF7F2] border border-ivory-200 text-xs text-slate-700">
                {{ $session->exercises_done ?? 'None recorded.' }}
            </div>
        </div>

        <!-- Clinical Notes -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Therapist Clinical Notes &amp; Observations</h3>
            <div class="p-3.5 rounded-xl bg-[#FAF7F2] border border-ivory-200 text-xs text-slate-700">
                {{ $session->therapist_notes ?? 'No clinical notes recorded.' }}
            </div>
        </div>

        <!-- Linked Billing & Invoice -->
        <div class="pt-4 border-t border-ivory-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-slate-500 font-semibold">Billing Mode:</span>
                <div class="font-bold text-slate-900 text-sm">
                    @if($session->billing_type === 'per_session')
                        Pay Per Session (Invoice Generated)
                    @else
                        Deducted from {{ $session->patientPackage->package->name ?? 'Prepaid Package' }}
                    @endif
                </div>
            </div>

            @if($session->invoice)
                <a href="{{ route('billing.show', $session->invoice->id) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-xs transition-colors">
                    View Invoice ({{ $session->invoice->invoice_no }}) &rarr;
                </a>
            @endif
        </div>

    </div>

</div>
@endsection
