<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentSessionController;
use Illuminate\Support\Facades\Route;

// Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated Application Routes (Protected by Auth Middleware)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (Tailored for Admin, Therapist, Receptionist)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Patients EMR (All authenticated staff)
    Route::resource('patients', PatientController::class);

    // Appointments & Home Service (All authenticated staff)
    Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store']);
    Route::patch('appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

    // Treatment Sessions (Bill Per Session & Package Deduction)
    Route::resource('sessions', TreatmentSessionController::class)->only(['index', 'create', 'store', 'show']);

    // Billing & Invoicing (Financial Desk: Admin & Receptionist only; restricted for Therapists)
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('billing/packages/create', [BillingController::class, 'createPackageInvoice'])->name('billing.create-package');
        Route::post('billing/packages', [BillingController::class, 'storePackageInvoice'])->name('billing.store-package');
        Route::get('billing/invoices/{id}', [BillingController::class, 'show'])->name('billing.show');
        Route::post('billing/invoices/{id}/pay', [BillingController::class, 'recordPayment'])->name('billing.record-payment');
    });

    // Exercise Library (All can view; Therapists & Admin can prescribe)
    Route::get('exercises', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::post('exercises/prescribe', [ExerciseController::class, 'prescribe'])
        ->middleware('role:admin,therapist')
        ->name('exercises.prescribe');
});
