<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\PrescriptionApiController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────
Route::post('/login',  [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Patients ─────────────────────────────────────
    Route::get   ('/patients',        [PatientApiController::class, 'index']);
    Route::post  ('/patients',        [PatientApiController::class, 'store']);
    Route::get   ('/patients/{patient}', [PatientApiController::class, 'show']);
    Route::put   ('/patients/{patient}', [PatientApiController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientApiController::class, 'destroy']);

    // ── Appointments ──────────────────────────────────
    Route::get   ('/appointments',                      [AppointmentApiController::class, 'index']);
    Route::post  ('/appointments',                      [AppointmentApiController::class, 'store']);
    Route::get   ('/appointments/{appointment}',        [AppointmentApiController::class, 'show']);
    Route::patch ('/appointments/{appointment}/status', [AppointmentApiController::class, 'updateStatus']);
    Route::delete('/appointments/{appointment}',        [AppointmentApiController::class, 'destroy']);

    // ── Invoices (admin + receptionist فقط) ──────────
    Route::get  ('/invoices',                        [InvoiceApiController::class, 'index']);
    Route::get  ('/invoices/{invoice}',              [InvoiceApiController::class, 'show']);
    Route::post ('/invoices/{invoice}/payment',      [InvoiceApiController::class, 'addPayment']);

    // ── Prescriptions ─────────────────────────────────
    Route::get  ('/prescriptions',                   [PrescriptionApiController::class, 'index']);
    Route::post ('/prescriptions',                   [PrescriptionApiController::class, 'store']);
    Route::get  ('/prescriptions/{prescription}',    [PrescriptionApiController::class, 'show']);

});
