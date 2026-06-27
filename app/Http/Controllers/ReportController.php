<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $month  = $request->get('month', now()->format('Y-m'));

        [$year, $mon] = explode('-', $month);
        $start = Carbon::create($year, $mon, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();

        // Income per day this month
        $dailyIncome = Payment::whereBetween('payment_date', [$start, $end])
            ->selectRaw('DATE(payment_date) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Totals
        $totalIncome  = $dailyIncome->sum('total');
        $totalPatients = Patient::whereBetween('created_at', [$start, $end])->count();
        $returningPatients = Appointment::whereBetween('starts_at', [$start, $end])
            ->distinct('patient_id')
            ->whereHas('patient', fn($q) => $q->where('created_at', '<', $start))
            ->count();

        $newPatients = $totalPatients;
        $pendingBalance = Invoice::selectRaw('SUM(total_amount - paid_amount) as bal')->value('bal') ?? 0;

        // Monthly income for last 6 months
        $monthlyIncome = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $monthlyIncome->push([
                'month' => $m->format('M Y'),
                'total' => Payment::whereYear('payment_date', $m->year)
                                  ->whereMonth('payment_date', $m->month)
                                  ->sum('amount'),
            ]);
        }

        return view('reports.index', compact(
            'dailyIncome', 'totalIncome', 'newPatients',
            'returningPatients', 'pendingBalance', 'monthlyIncome', 'month'
        ));
    }
}
