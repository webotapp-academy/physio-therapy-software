@extends('layouts.app')

@section('title', 'Exercise Library & Digital Home Care')

@section('content')
<div class="py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Physiotherapy Exercise Library</h1>
            <p class="text-xs text-slate-500 mt-1">Standardized therapeutic exercises, instructions, precautions, and patient home care regimes.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-[#FDFCF9] p-4 rounded-2xl border border-ivory-300 shadow-xs">
        <form action="{{ route('exercises.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Target Body Region</label>
                <select name="target" class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2 bg-[#FAF7F2] focus:bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="">All Body Regions</option>
                    <option value="Cervical" {{ request('target') === 'Cervical' ? 'selected' : '' }}>Cervical (Neck)</option>
                    <option value="Lumbar" {{ request('target') === 'Lumbar' ? 'selected' : '' }}>Lumbar (Lower Back &amp; Core)</option>
                    <option value="Knee" {{ request('target') === 'Knee' ? 'selected' : '' }}>Knee &amp; Quadriceps</option>
                    <option value="Shoulder" {{ request('target') === 'Shoulder' ? 'selected' : '' }}>Shoulder &amp; Rotator Cuff</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Category</label>
                <select name="category" class="w-full text-xs rounded-xl border border-ivory-300 px-3 py-2 bg-[#FAF7F2] focus:bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="">All Categories</option>
                    <option value="strengthening" {{ request('category') === 'strengthening' ? 'selected' : '' }}>Strengthening</option>
                    <option value="mobility" {{ request('category') === 'mobility' ? 'selected' : '' }}>Mobility / Range of Motion</option>
                    <option value="postural" {{ request('category') === 'postural' ? 'selected' : '' }}>Postural Correction</option>
                    <option value="stretching" {{ request('category') === 'stretching' ? 'selected' : '' }}>Stretching</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition-colors shadow-xs">
                    Filter Library
                </button>
                @if(request()->hasAny(['target', 'category']))
                    <a href="{{ route('exercises.index') }}" class="bg-ivory-200 hover:bg-ivory-300 text-slate-700 text-xs font-semibold py-2 px-3 rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Exercise Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($exercises as $exercise)
            <div class="bg-[#FDFCF9] rounded-2xl border border-ivory-300 shadow-xs p-6 flex flex-col justify-between hover:border-brand-300 transition-colors">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $exercise->target_body_part }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-ivory-200 text-slate-700 ml-1 capitalize">
                                {{ $exercise->category }}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900 mt-2">{{ $exercise->name }}</h3>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 capitalize border border-ivory-300 px-2 py-0.5 rounded bg-ivory-50">
                            {{ $exercise->difficulty_level }}
                        </span>
                    </div>

                    <div class="mt-3 text-xs text-slate-600 space-y-2">
                        <div>
                            <strong class="text-slate-800">Instructions:</strong>
                            <p class="mt-0.5 text-slate-600 leading-relaxed">{{ $exercise->instructions }}</p>
                        </div>
                        @if($exercise->precautions)
                            <div class="text-amber-800 bg-amber-50/70 p-2.5 rounded-xl border border-amber-200/60 text-[11px]">
                                <strong>Precaution:</strong> {{ $exercise->precautions }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Prescribe Exercise Form -->
                <div class="mt-5 pt-4 border-t border-ivory-200">
                    <form action="{{ route('exercises.prescribe') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="exercise_id" value="{{ $exercise->id }}">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Assign Patient</label>
                                <select name="patient_id" required class="w-full text-xs rounded-lg border border-ivory-300 px-2.5 py-1.5 bg-[#FAF7F2] focus:bg-white">
                                    <option value="">-- Choose Patient --</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Prescribing Doctor</label>
                                <select name="therapist_id" required class="w-full text-xs rounded-lg border border-ivory-300 px-2.5 py-1.5 bg-[#FAF7F2] focus:bg-white">
                                    @foreach($therapists as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Sets x Reps</label>
                                <div class="flex items-center gap-1">
                                    <input type="number" name="sets" value="3" min="1" max="10" class="w-full text-xs rounded-lg border border-ivory-300 px-2 py-1 text-center font-bold">
                                    <span>x</span>
                                    <input type="number" name="repetitions" value="10" min="1" max="50" class="w-full text-xs rounded-lg border border-ivory-300 px-2 py-1 text-center font-bold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Hold (Sec)</label>
                                <input type="number" name="hold_seconds" value="5" min="0" max="60" class="w-full text-xs rounded-lg border border-ivory-300 px-2 py-1 text-center font-bold">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Times Daily</label>
                                <input type="number" name="frequency_times_daily" value="2" min="1" max="5" class="w-full text-xs rounded-lg border border-ivory-300 px-2 py-1 text-center font-bold">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold py-2 rounded-xl shadow-xs transition-colors">
                            Prescribe to Patient Home Program &rarr;
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center text-slate-400 py-12">
                No exercises found matching your filter criteria.
            </div>
        @endforelse
    </div>

    @if($exercises->hasPages())
        <div class="p-4 bg-[#FDFCF9] rounded-2xl border border-ivory-300">
            {{ $exercises->links() }}
        </div>
    @endif

</div>
@endsection
