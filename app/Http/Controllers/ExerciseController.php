<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Patient;
use App\Models\PrescribedExercise;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Exercise::where('is_active', true);

        if ($request->filled('target')) {
            $query->where('target_body_part', 'like', "%{$request->input('target')}%");
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $exercises = $query->paginate(12)->withQueryString();
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();

        return view('exercises.index', compact('exercises', 'patients', 'therapists'));
    }

    public function prescribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'therapist_id' => 'required|exists:users,id',
            'exercise_id' => 'required|exists:exercises,id',
            'sets' => 'required|integer|min:1|max:10',
            'repetitions' => 'required|integer|min:1|max:50',
            'hold_seconds' => 'required|integer|min:0|max:120',
            'frequency_times_daily' => 'required|integer|min:1|max:6',
            'notes' => 'nullable|string|max:255',
        ]);

        PrescribedExercise::create($validated);

        return redirect()->route('patients.show', $validated['patient_id'])
            ->with('success', 'Exercise prescribed successfully to patient home care plan!');
    }
}
