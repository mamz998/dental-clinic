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
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'total_patients'     => Patient::count(),
            'new_patients_month' => Patient::where('created_at', '>=', $thisMonth)->count(),
            'appointments_today' => Appointment::whereDate('starts_at', $today)->count(),
            'income_today'       => Payment::whereDate('payment_date', $today)->sum('amount'),
            'income_month'       => Payment::where('payment_date', '>=', $thisMonth)->sum('amount'),
            'pending_balance'    => Invoice::selectRaw('SUM(total_amount - paid_amount) as bal')->value('bal') ?? 0,
        ];

        $todayAppointments = Appointment::with('patient')
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $recentPatients = Patient::latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'todayAppointments', 'recentPatients'));
    }
}
