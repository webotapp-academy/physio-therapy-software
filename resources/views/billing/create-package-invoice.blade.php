@extends('layouts.app')

@section('title', 'Sell Treatment Package')

@section('content')
<div class="py-6 max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Sell Treatment Package</h1>
            <p class="text-xs text-slate-500 mt-1">Issue a multi-session discounted package (e.g. 10 or 20 visits) for clinic or home care.</p>
        </div>
        <a href="{{ route('billing.index') }}" class="text-xs font-bold text-slate-600 hover:text-brand-600">
            &larr; Back to Billing
        </a>
    </div>

    <form action="{{ route('billing.store-package') }}" method="POST" class="bg-[#FDFCF9] rounded-2xl border border-ivory-300 shadow-xs p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Select Patient *</label>
            <select name="patient_id" required class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-[#FAF7F2] focus:bg-white">
                <option value="">-- Choose Patient --</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>
                        {{ $patient->full_name }} ({{ $patient->patient_uid }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Select Therapy Package *</label>
            <select name="package_id" id="packageSelect" required onchange="updatePrice()" class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2.5 focus:ring-2 focus:ring-brand-500 bg-[#FAF7F2] focus:bg-white">
                <option value="">-- Choose Package --</option>
                @foreach($packages as $pkg)
                    <option value="{{ $pkg->id }}" data-price="{{ $pkg->price }}" data-sessions="{{ $pkg->total_sessions }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }}>
                        {{ $pkg->name }} ({{ $pkg->total_sessions }} Sessions) - ₹{{ number_format($pkg->price, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Package Price (₹)</label>
                <input type="text" id="displayPrice" readonly class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2.5 bg-ivory-100 font-extrabold text-slate-900" value="₹0.00">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Special Discount (₹)</label>
                <input type="number" name="discount_amount" id="discountAmount" value="0.00" oninput="updateTotalDue()" class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2.5 bg-[#FAF7F2] focus:bg-white">
            </div>
        </div>

        <!-- Payment Mode & Reference -->
        <div class="p-4 rounded-xl bg-[#FAF7F2] border border-ivory-200 space-y-3">
            <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-slate-700">Net Amount to Collect:</span>
                <span class="text-lg text-brand-700 font-black" id="netAmountDisplay">₹0.00</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-ivory-200">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2 bg-white">
                        <option value="upi">UPI / GPay / PhonePe</option>
                        <option value="cash">Cash</option>
                        <option value="card">Debit / Credit Card</option>
                        <option value="bank_transfer">Bank NEFT/RTGS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Txn Ref / Cheque No</label>
                    <input type="text" name="transaction_reference" class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2 bg-white" placeholder="e.g. UPI Ref #839401">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('billing.index') }}" class="px-5 py-2.5 rounded-xl border border-ivory-300 text-xs font-bold text-slate-700 hover:bg-ivory-100 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-600/20 transition-all">
                Generate Invoice &amp; Settle Package
            </button>
        </div>
    </form>

</div>

<script>
    let basePrice = 0;
    function updatePrice() {
        const select = document.getElementById('packageSelect');
        const selected = select.options[select.selectedIndex];
        basePrice = parseFloat(selected.getAttribute('data-price')) || 0;
        document.getElementById('displayPrice').value = '₹' + basePrice.toFixed(2);
        updateTotalDue();
    }

    function updateTotalDue() {
        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        const net = Math.max(0, basePrice - discount);
        document.getElementById('netAmountDisplay').innerText = '₹' + net.toFixed(2);
    }
</script>
@endsection
