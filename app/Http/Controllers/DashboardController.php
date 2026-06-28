<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // الدكتور يشوف بياناته بس
        if ($user->isDoctor()) {
            return $this->doctorDashboard($user, $today, $thisMonth);
        }

        // المدير والاستقبال يشوفوا الكل (الاستقبال بدون أرقام مالية)
        $stats = [
            'total_patients'     => Patient::count(),
            'new_patients_month' => Patient::where('created_at', '>=', $thisMonth)->count(),
            'appointments_today' => Appointment::whereDate('starts_at', $today)->count(),
            'income_today'       => $user->isAdmin() ? Payment::whereDate('payment_date', $today)->sum('amount') : null,
            'income_month'       => $user->isAdmin() ? Payment::where('payment_date', '>=', $thisMonth)->sum('amount') : null,
            'pending_balance'    => $user->isAdmin() ? (Invoice::selectRaw('SUM(total_amount - paid_amount) as bal')->value('bal') ?? 0) : null,
        ];

        $todayAppointments = Appointment::with('patient', 'doctor')
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $recentPatients = Patient::with('doctor')->latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'todayAppointments', 'recentPatients'));
    }

    private function doctorDashboard($user, $today, $thisMonth)
    {
        $myPatients = Patient::where('doctor_id', $user->id);

        $stats = [
            'my_patients'        => $myPatients->count(),
            'new_patients_month' => (clone $myPatients)->where('created_at', '>=', $thisMonth)->count(),
            'appointments_today' => Appointment::where('doctor_id', $user->id)->whereDate('starts_at', $today)->count(),
            'appointments_month' => Appointment::where('doctor_id', $user->id)->where('starts_at', '>=', $thisMonth)->count(),
        ];

        $todayAppointments = Appointment::with('patient')
            ->where('doctor_id', $user->id)
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $recentPatients = Patient::with('medicalHistory')
            ->where('doctor_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard-doctor', compact('stats', 'todayAppointments', 'recentPatients', 'user'));
    }
}
