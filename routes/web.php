<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patients — كل المستخدمين
    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/teeth/{tooth}', [PatientController::class, 'updateTooth'])->name('patients.tooth.update');
    Route::post('patients/{patient}/files', [PatientController::class, 'uploadFile'])->name('patients.files.upload');

    // Appointments — كل المستخدمين
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    // Invoices — مدير واستقبال فقط (مش دكتور)
    Route::middleware('role:admin,receptionist')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.payment');
    });

    // Prescriptions — كل المستخدمين
    Route::resource('prescriptions', PrescriptionController::class);

    // Reports — مدير فقط
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Users — مدير فقط
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
