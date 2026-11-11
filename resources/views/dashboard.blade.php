@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6 space-y-6">

    <!-- Top Clinic Banner tailored by Role -->
    <div class="bg-gradient-to-r from-brand-800 via-brand-700 to-brand-600 rounded-2xl p-6 text-white shadow-lg shadow-brand-900/15 flex flex-col md:flex-row md:items-center justify-between gap-4 border border-brand-700/40">
        <div>
            <div class="inline-flex items-center gap-2 bg-brand-900/50 text-brand-100 text-xs font-semibold px-3 py-1 rounded-full border border-brand-400/30 mb-2">
                <span class="w-2 h-2 rounded-full bg-brand-300 animate-pulse"></span>
                @if($role === 'therapist')
                    Physiotherapist Clinical EMR &bull; {{ auth()->user()->name }}
                @elseif($role === 'receptionist')
                    Front Desk Reception &bull; Billing &amp; Appointments
                @else
                    Clinic Director Oversight &bull; Database: physio
                @endif
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight">
                @if($role === 'therapist')
                    Doctor's Clinical Workspace
                @elseif($role === 'receptionist')
                    Front Desk &amp; Patient Registry
                @else
                    Physiotherapy Center Dashboard
                @endif
            </h1>
            <p class="text-brand-100 text-sm mt-1 max-w-xl">
                @if($role === 'therapist')
                    Monitor your scheduled clinic &amp; home patients, log treatment modalities, record VAS pain outcomes, and prescribe exercises.
                @elseif($role === 'receptionist')
                    Manage patient registrations, schedule in-clinic &amp; home visits, collect session fees, and issue package receipts.
                @else
                    Managing in-clinic rehabilitation, domiciliary home visits, per-session invoicing, therapist performance, and revenue.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if(in_array($role, ['admin', 'receptionist'], true))
                <a href="{{ route('patients.create') }}" class="bg-ivory-50 text-brand-800 hover:bg-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-2 border border-brand-300/40">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Register Patient
                </a>
            @endif

            <a href="{{ route('appointments.create') }}" class="bg-brand-500 hover:bg-brand-400 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Schedule Visit
            </a>

            @if(in_array($role, ['admin', 'therapist'], true))
                <a href="{{ route('sessions.create') }}" class="bg-brand-900/60 hover:bg-brand-900 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all flex items-center gap-2 border border-white/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Record Session
                </a>
            @endif
        </div>
    </div>

    <!-- 4 Key Stat Cards - Tailored by Role -->
    @if($role === 'therapist')
        <!-- THERAPIST STATS (No Billing Data) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: My Today's Visits -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">My Today's Visits</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['my_today_appointments'] }}</span>
                    <span class="text-xs text-[#6B7869]">patients</span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-brand-100 text-brand-800 border border-brand-200">
                        🏥 {{ $stats['my_clinic_visits'] }} Clinic
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-ivory-200 text-[#445C42] border border-[#D5CBB9]">
                        🚗 {{ $stats['my_home_visits'] }} Home Visits
                    </span>
                </div>
            </div>

            <!-- Card 2: Active Treatment Plans -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Active Treatment Plans</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['my_active_plans'] }}</span>
                    <span class="text-xs text-brand-700 font-semibold">Protocols Active</span>
                </div>
                <div class="mt-3 text-xs text-[#6B7869]">
                    <span>Assigned Under Your Clinical Care</span>
                </div>
            </div>

            <!-- Card 3: Completed Therapy Sessions -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">My Completed Sessions</span>
                    <span class="p-2 rounded-xl bg-brand-100 text-brand-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-brand-800">{{ $stats['my_completed_sessions'] }}</span>
                    <span class="text-xs text-[#6B7869]">sessions logged</span>
                </div>
                <div class="mt-3 text-xs text-brand-700 font-semibold">
                    <span>Includes VAS &amp; Modality Logs</span>
                </div>
            </div>

            <!-- Card 4: Patients Treated -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Unique Patients Treated</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['my_assigned_patients'] }}</span>
                    <span class="text-xs text-[#6B7869]">patients</span>
                </div>
                <div class="mt-3 text-xs text-brand-700 font-semibold">
                    <a href="{{ route('patients.index') }}" class="hover:underline">Browse Patient EMRs &rarr;</a>
                </div>
            </div>
        </div>

    @elseif($role === 'receptionist')
        <!-- RECEPTIONIST STATS (Desk Collections & Queue) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Today's Appointments -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Today's Schedule</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['today_appointments'] }}</span>
                    <span class="text-xs text-[#6B7869]">appointments</span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-brand-100 text-brand-800 border border-brand-200">
                        🏥 {{ $stats['today_clinic_appointments'] }} Clinic
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-ivory-200 text-[#445C42] border border-[#D5CBB9]">
                        🚗 {{ $stats['today_home_visits'] }} Home Visits
                    </span>
                </div>
            </div>

            <!-- Card 2: Registered Patients -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Total Patients</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['total_patients'] }}</span>
                    <span class="text-xs text-brand-700 font-semibold">Registered EMRs</span>
                </div>
                <div class="mt-3 text-xs text-[#6B7869]">
                    <span>Duty Therapists: <strong>{{ $stats['active_therapists'] }} Doctors</strong></span>
                </div>
            </div>

            <!-- Card 3: Today's Collections -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Today's Collections</span>
                    <span class="p-2 rounded-xl bg-brand-100 text-brand-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-brand-800">₹{{ number_format($stats['today_collections'], 2) }}</span>
                </div>
                <div class="mt-3 text-xs text-[#5C6B5A]">
                    <span>Settled today via Cash/UPI/Card</span>
                </div>
            </div>

            <!-- Card 4: Pending Invoices -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Invoices Awaiting Payment</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold {{ $stats['pending_invoices_count'] > 0 ? 'text-amber-700' : 'text-[#1F2C1E]' }}">
                        {{ $stats['pending_invoices_count'] }}
                    </span>
                    <span class="text-xs text-[#6B7869]">unpaid</span>
                </div>
                <div class="mt-3 text-xs">
                    <a href="{{ route('billing.index', ['status' => 'unpaid']) }}" class="text-brand-700 font-semibold hover:underline">
                        Total Due: ₹{{ number_format($stats['unpaid_balance_total'], 2) }} &rarr;
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- ADMIN STATS (Full Overview) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Today's Appointments -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Today's Visits</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['today_appointments'] }}</span>
                    <span class="text-xs text-[#6B7869]">appointments</span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-brand-100 text-brand-800 border border-brand-200">
                        🏥 {{ $stats['today_clinic_appointments'] }} Clinic
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full font-semibold bg-ivory-200 text-[#445C42] border border-[#D5CBB9]">
                        🚗 {{ $stats['today_home_visits'] }} Home
                    </span>
                </div>
            </div>

            <!-- Card 2: Registered Patients -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Total Patients</span>
                    <span class="p-2 rounded-xl bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#1F2C1E]">{{ $stats['total_patients'] }}</span>
                    <span class="text-xs text-brand-700 font-semibold">Active EMRs</span>
                </div>
                <div class="mt-3 text-xs text-[#6B7869] flex items-center justify-between">
                    <span>Active Therapists:</span>
                    <span class="font-bold text-[#1F2C1E]">{{ $stats['active_therapists'] }} Doctors</span>
                </div>
            </div>

            <!-- Card 3: Total Collections -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Revenue Collected</span>
                    <span class="p-2 rounded-xl bg-brand-100 text-brand-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-brand-800">₹{{ number_format($stats['total_revenue'], 2) }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-[#5C6B5A]">
                    <span>Per Session: <strong class="text-[#1F2C1E]">₹{{ number_format($stats['per_session_revenue'], 0) }}</strong></span>
                    <span>Packages: <strong class="text-[#1F2C1E]">₹{{ number_format($stats['package_revenue'], 0) }}</strong></span>
                </div>
            </div>

            <!-- Card 4: Unpaid Single Sessions Alert -->
            <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-[#E6DFD3] shadow-xs hover:border-brand-300 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#6B7869]">Pay-Per-Session Dues</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold {{ $stats['unpaid_sessions_count'] > 0 ? 'text-amber-700' : 'text-[#1F2C1E]' }}">
                        {{ $stats['unpaid_sessions_count'] }}
                    </span>
                    <span class="text-xs text-[#6B7869]">sessions pending bill</span>
                </div>
                <div class="mt-3 text-xs">
                    @if($stats['unpaid_sessions_count'] > 0)
                        <a href="{{ route('billing.index', ['status' => 'unpaid']) }}" class="text-brand-700 font-semibold hover:underline">Collect Outstanding &rarr;</a>
                    @else
                        <span class="text-brand-700 font-semibold">✓ All sessions settled</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Main Section: Two Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Appointments & Home Visits -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E6DFD3] flex items-center justify-between bg-white">
                    <div>
                        <h2 class="text-base font-bold text-[#1F2C1E] flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-600"></span>
                            {{ $role === 'therapist' ? "My Patient Schedule Today" : "Appointments & Home Visits Schedule" }}
                        </h2>
                        <p class="text-xs text-[#6B7869] mt-0.5">
                            {{ $role === 'therapist' ? "Your allocated clinic patients and domiciliary visits" : "Real-time status for in-clinic queues and mobile physiotherapists" }}
                        </p>
                    </div>
                    <a href="{{ route('appointments.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-900">
                        View Full Calendar &rarr;
                    </a>
                </div>

                <div class="divide-y divide-[#E6DFD3]/60">
                    @forelse($todayAppointments as $apt)
                        <div class="p-5 hover:bg-ivory-100/60 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <!-- Time / Token Badge -->
                                <div class="w-16 h-16 rounded-xl bg-ivory-200 border border-[#E6DFD3] flex flex-col items-center justify-center shrink-0">
                                    <span class="text-xs font-extrabold text-[#1F2C1E]">{{ \Carbon\Carbon::parse($apt->start_time)->format('h:i') }}</span>
                                    <span class="text-[10px] uppercase font-bold text-[#6B7869]">{{ \Carbon\Carbon::parse($apt->start_time)->format('A') }}</span>
                                    @if($apt->token_number)
                                        <span class="text-[9px] bg-brand-100 text-brand-800 px-1 rounded font-bold mt-0.5">Token #{{ $apt->token_number }}</span>
                                    @endif
                                </div>

                                <!-- Appointment Info -->
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('patients.show', $apt->patient_id) }}" class="text-sm font-bold text-[#1F2C1E] hover:text-brand-700">
                                            {{ $apt->patient->full_name }}
                                        </a>
                                        <span class="text-xs text-[#8A7D69] font-mono">({{ $apt->patient->patient_uid }})</span>

                                        <!-- Location Badge -->
                                        @if($apt->service_location === 'home_visit')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-ivory-300 text-[#3C4A3A] border border-[#D5CBB9]">
                                                🚗 Home Service ({{ $apt->travel_distance_km }} km)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-800 border border-brand-200">
                                                🏥 In-Clinic
                                            </span>
                                        @endif

                                        <!-- Status Badge -->
                                        @if($apt->status === 'completed')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-brand-100 text-brand-800">Completed</span>
                                        @elseif($apt->status === 'in_progress')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 animate-pulse">In Treatment</span>
                                        @elseif($apt->status === 'checked_in')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-brand-50 text-brand-700">Checked In</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-ivory-200 text-[#4A5749]">Scheduled</span>
                                        @endif
                                    </div>

                                    <div class="mt-1 text-xs text-[#5C6B5A] space-y-0.5">
                                        <div>
                                            @if($role !== 'therapist')
                                                <strong>Therapist:</strong> {{ $apt->therapist->name }} &bull;
                                            @endif
                                            <strong>Service:</strong> {{ $apt->service->name ?? 'Physiotherapy' }}
                                        </div>
                                        @if($apt->service_location === 'home_visit')
                                            <div class="text-[#3C4A3A] font-medium flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ $apt->home_address }} @if($apt->home_landmark) (Near {{ $apt->home_landmark }}) @endif
                                                @if($role !== 'therapist')
                                                    &bull; Travel Fee: ₹{{ number_format($apt->travel_charge, 2) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                @if($apt->status !== 'completed')
                                    @if(in_array($role, ['admin', 'therapist'], true))
                                        <a href="{{ route('sessions.create', ['patient_id' => $apt->patient_id, 'appointment_id' => $apt->id]) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow-xs flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Record Session
                                        </a>
                                    @else
                                        <a href="{{ route('appointments.index') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-ivory-200 text-[#3C4A3A] hover:bg-ivory-300 border border-[#D5CBB9]">
                                            Manage Queue &rarr;
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-brand-700 font-semibold flex items-center gap-1">
                                        ✓ Completed
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-[#8A7D69] text-sm">
                            No appointments found for today.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Clinical Sessions & VAS Pain Log -->
            <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E6DFD3] flex items-center justify-between bg-white">
                    <div>
                        <h2 class="text-base font-bold text-[#1F2C1E]">
                            {{ $role === 'therapist' ? "My Recent Clinical Sessions" : "Recent Treatment Sessions" }}
                        </h2>
                        <p class="text-xs text-[#6B7869]">Tracking recovery progress, modalities &amp; VAS pain scores</p>
                    </div>
                    <a href="{{ route('sessions.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-900">
                        View All &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-ivory-100 text-[#5C6B5A] font-bold uppercase border-b border-[#E6DFD3]">
                            <tr>
                                <th class="px-4 py-3">Patient</th>
                                @if($role !== 'therapist')
                                    <th class="px-4 py-3">Therapist</th>
                                @endif
                                <th class="px-4 py-3">Pain (VAS)</th>
                                <th class="px-4 py-3">Modalities Used</th>
                                <th class="px-4 py-3">Billing Mode</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E6DFD3]/60">
                            @foreach($recentSessions as $session)
                                <tr class="hover:bg-ivory-100/50">
                                    <td class="px-4 py-3 font-semibold text-[#1F2C1E]">
                                        <a href="{{ route('patients.show', $session->patient_id) }}" class="hover:text-brand-700">
                                            {{ $session->patient->full_name }}
                                        </a>
                                        <div class="text-[10px] text-[#8A7D69]">Session #{{ $session->session_number }}</div>
                                    </td>
                                    @if($role !== 'therapist')
                                        <td class="px-4 py-3 text-[#5C6B5A]">{{ $session->therapist->name }}</td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded font-bold {{ $session->current_pain_vas <= 3 ? 'bg-brand-100 text-brand-800' : ($session->current_pain_vas <= 6 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $session->current_pain_vas }}/10
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-[#5C6B5A] max-w-xs truncate">
                                        {{ is_array($session->modalities_used) ? implode(', ', $session->modalities_used) : $session->modalities_used }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($session->billing_type === 'per_session')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-brand-100 text-brand-800 border border-brand-200">
                                                Bill Per Session
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-ivory-200 text-[#4A5749] border border-[#E6DFD3]">
                                                Package Deduction
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('sessions.show', $session->id) }}" class="font-bold text-brand-700 hover:text-brand-900">View &rarr;</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 3: Tailored Right Sidebar Widgets -->
        <div class="space-y-6">

            @if($role === 'therapist')
                <!-- THERAPIST WIDGET: Exercise Prescriptions & Home Care -->
                <div class="bg-gradient-to-br from-brand-50 to-ivory-200 border border-brand-200 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-brand-900 font-bold text-sm mb-1">
                        <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Clinical Prescriptions
                    </div>
                    <p class="text-xs text-brand-800/80 mb-4">Prescribe home exercise regimes, precautions, and set/rep targets.</p>

                    <div class="space-y-2.5">
                        <a href="{{ route('exercises.index') }}" class="w-full bg-white hover:bg-ivory-50 border border-brand-200 text-[#2B352A] text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-between shadow-xs transition-colors">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                                Browse Exercise Library
                            </span>
                            <span class="text-brand-700">&rarr;</span>
                        </a>
                        <a href="{{ route('sessions.create') }}" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-between shadow-xs transition-colors">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                Log Session &amp; VAS Outcome
                            </span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

            @else
                <!-- ADMIN & RECEPTIONIST: Billing Shortcuts -->
                <div class="bg-gradient-to-br from-brand-50 to-ivory-200 border border-brand-200 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 text-brand-900 font-bold text-sm mb-1">
                        <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Billing Desk Shortcuts
                    </div>
                    <p class="text-xs text-brand-800/80 mb-4">Choose between Pay-Per-Session instant receipt or Sell a Multi-Session Package.</p>

                    <div class="space-y-2.5">
                        <a href="{{ route('billing.index') }}" class="w-full bg-white hover:bg-ivory-50 border border-brand-200 text-[#2B352A] text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-between shadow-xs transition-colors">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                                Billing Register &amp; Receipts
                            </span>
                            <span class="text-brand-700">&rarr;</span>
                        </a>
                        <a href="{{ route('billing.create-package') }}" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2.5 px-3 rounded-xl flex items-center justify-between shadow-xs transition-colors">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                                Sell Treatment Package (10/20 Sessions)
                            </span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Invoices Widget (Admin & Receptionist) -->
                <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-[#1F2C1E]">Recent Invoices</h3>
                        <a href="{{ route('billing.index') }}" class="text-xs font-bold text-brand-700 hover:underline">All &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentInvoices as $inv)
                            <div class="p-3 rounded-xl bg-ivory-100 border border-[#E6DFD3] flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-[#1F2C1E]">{{ $inv->patient->full_name }}</div>
                                    <div class="text-[10px] text-[#8A7D69] font-mono">{{ $inv->invoice_no }} &bull; {{ $inv->invoice_date->format('d M') }}</div>
                                    <div class="mt-1">
                                        <span class="text-[9px] uppercase px-1.5 py-0.5 rounded font-bold {{ $inv->billing_model === 'bill_per_session' ? 'bg-brand-100 text-brand-800 border border-brand-200' : 'bg-ivory-200 text-[#4A5749] border border-[#D5CBB9]' }}">
                                            {{ str_replace('_', ' ', $inv->billing_model) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-extrabold text-[#1F2C1E]">₹{{ number_format($inv->total_amount, 2) }}</div>
                                    <span class="text-[10px] font-bold {{ $inv->payment_status === 'paid' ? 'text-brand-700' : 'text-amber-700' }}">
                                        {{ ucfirst($inv->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-[#8A7D69] text-center py-4">No recent invoices</div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Portable Equipment Checkout status for Home Visits (All Roles) -->
            <div class="bg-[#FDFCF9] rounded-2xl border border-[#E6DFD3] shadow-xs p-5">
                <h3 class="text-sm font-bold text-[#1F2C1E] mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    Home Visit Portable Kits
                </h3>
                <p class="text-xs text-[#6B7869] mb-3">Equipment ready for dispatch to domiciliary visits:</p>
                <div class="text-xs space-y-2">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-brand-50 text-brand-800 border border-brand-200">
                        <span class="font-semibold">Portable IFT/TENS Unit</span>
                        <span class="text-[10px] bg-brand-200 px-1.5 py-0.5 rounded font-bold">Ready</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-ivory-200 text-[#4A5749] border border-[#E6DFD3]">
                        <span class="font-semibold">Ultrasound 1MHz Mobile</span>
                        <span class="text-[10px] bg-[#E6DFD3] px-1.5 py-0.5 rounded font-bold">In Clinic</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
