@extends('layouts.app')

@section('title', 'Billing & Invoices')

@section('content')
<div class="py-6 space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Billing &amp; Invoices Desk</h1>
            <p class="text-xs text-slate-500 mt-1">Manage single session receipts, home travel allowances, prepaid packages, and collection registers.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3.5 py-2 border border-brand-600 text-brand-700 bg-brand-50 hover:bg-brand-100 text-xs font-bold rounded-xl shadow-xs transition-colors">
                + Bill Single Session
            </a>
            <a href="{{ route('billing.create-package') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                + Sell Therapy Package
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-ivory-300 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Billed</span>
            <div class="text-2xl font-black text-slate-900 mt-2">₹{{ number_format($stats['total_invoiced'], 2) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Gross Invoiced Revenue</div>
        </div>

        <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-ivory-300 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Collections</span>
            <div class="text-2xl font-black text-brand-700 mt-2">₹{{ number_format($stats['total_collected'], 2) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Cash / UPI / Card Settled</div>
        </div>

        <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-ivory-300 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pay-Per-Session Collections</span>
            <div class="text-2xl font-black text-slate-800 mt-2">₹{{ number_format($stats['per_session_collected'], 2) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Single Session Pay-As-You-Go</div>
        </div>

        <div class="bg-[#FDFCF9] p-5 rounded-2xl border border-ivory-300 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Package Upfront Collections</span>
            <div class="text-2xl font-black text-brand-800 mt-2">₹{{ number_format($stats['packages_collected'], 2) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">10/20 Session Prepaid Bundles</div>
        </div>
    </div>

    <!-- Filters & Invoices Table -->
    <div class="bg-[#FDFCF9] rounded-2xl border border-ivory-300 shadow-xs overflow-hidden">
        <!-- Filter Tabs -->
        <div class="p-4 border-b border-ivory-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-2 text-xs font-bold">
                <a href="{{ route('billing.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('model') ? 'bg-brand-600 text-white' : 'bg-[#FAF7F2] text-slate-700 hover:bg-ivory-200 border border-ivory-300' }}">
                    All Invoices
                </a>
                <a href="{{ route('billing.index', ['model' => 'bill_per_session']) }}" class="px-3 py-1.5 rounded-lg {{ request('model') === 'bill_per_session' ? 'bg-brand-600 text-white' : 'bg-[#FAF7F2] text-slate-700 hover:bg-ivory-200 border border-ivory-300' }}">
                    Per-Session Invoices
                </a>
                <a href="{{ route('billing.index', ['model' => 'package_upfront']) }}" class="px-3 py-1.5 rounded-lg {{ request('model') === 'package_upfront' ? 'bg-brand-600 text-white' : 'bg-[#FAF7F2] text-slate-700 hover:bg-ivory-200 border border-ivory-300' }}">
                    Package Invoices
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('billing.index', array_merge(request()->query(), ['status' => 'unpaid'])) }}" class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-300 px-3 py-1.5 rounded-lg hover:bg-amber-100">
                    Show Unpaid Invoices Only
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#FAF7F2] text-slate-600 font-bold uppercase border-b border-ivory-300 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Invoice #</th>
                        <th class="px-4 py-3.5">Patient Details</th>
                        <th class="px-4 py-3.5">Billing Type</th>
                        <th class="px-4 py-3.5">Breakdown (Session + Travel)</th>
                        <th class="px-4 py-3.5">Total Amount</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">View / Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ivory-200">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-[#FAF7F2]/80 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-brand-700 text-sm">{{ $inv->invoice_no }}</span>
                                <div class="text-[10px] text-slate-400">{{ $inv->invoice_date->format('d M, Y') }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-bold text-slate-900 text-sm hover:text-brand-600">
                                    {{ $inv->patient->full_name }}
                                </a>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $inv->patient->patient_uid }}</div>
                            </td>

                            <td class="px-4 py-4">
                                @if($inv->billing_model === 'bill_per_session')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-800 border border-brand-200">
                                        Single Session Bill
                                    </span>
                                @elseif($inv->billing_model === 'package_upfront')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Treatment Package
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ str_replace('_', ' ', $inv->billing_model) }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-slate-600 text-[11px]">
                                <div>Session: ₹{{ number_format($inv->service_amount, 2) }}</div>
                                @if($inv->travel_charge > 0)
                                    <div class="text-amber-800 font-semibold">+ Travel: ₹{{ number_format($inv->travel_charge, 2) }}</div>
                                @endif
                                @if($inv->consumables_amount > 0)
                                    <div class="text-slate-500">+ Consumables: ₹{{ number_format($inv->consumables_amount, 2) }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-4 font-black text-slate-900 text-sm">
                                ₹{{ number_format($inv->total_amount, 2) }}
                                @if($inv->balance_amount > 0)
                                    <div class="text-[10px] text-rose-600 font-bold">Due: ₹{{ number_format($inv->balance_amount, 2) }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $inv->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->payment_status === 'partial' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ ucfirst($inv->payment_status) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('billing.show', $inv->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 transition-colors">
                                    Invoice &amp; Receipt &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-ivory-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
