<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

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
        $totalIncome     = $dailyIncome->sum('total');
        $newPatients     = Patient::whereBetween('created_at', [$start, $end])->count();
        $returningPatients = Appointment::whereBetween('starts_at', [$start, $end])
            ->distinct('patient_id')
            ->whereHas('patient', fn($q) => $q->where('created_at', '<', $start))
            ->count();
        $pendingBalance  = Invoice::selectRaw('SUM(total_amount - paid_amount) as bal')->value('bal') ?? 0;

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

        // ── تقرير الدكاترة ──────────────────────────────────────────────
        $doctors = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with('doctor')
            ->get()
            ->map(function ($dr) use ($start, $end) {
                // الحالات (الفواتير) في الفترة
                $invoicesInPeriod = Invoice::where('user_id', $dr->id)
                    ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
                    ->get();

                $casesCount   = $invoicesInPeriod->count();
                $totalBilled  = $invoicesInPeriod->sum('total_amount');
                $totalCollected = $invoicesInPeriod->sum('paid_amount');
                $pending      = $totalBilled - $totalCollected;
                $commission   = $dr->doctor?->commission_rate ?? 0;
                $drShare      = round($totalCollected * ($commission / 100));

                // المواعيد في الفترة
                $apptTotal     = Appointment::where('doctor_id', $dr->id)
                    ->whereBetween('starts_at', [$start, $end])->count();
                $apptCompleted = Appointment::where('doctor_id', $dr->id)
                    ->whereBetween('starts_at', [$start, $end])
                    ->where('status', 'completed')->count();
                $apptCancelled = Appointment::where('doctor_id', $dr->id)
                    ->whereBetween('starts_at', [$start, $end])
                    ->whereIn('status', ['cancelled', 'no_show'])->count();

                // إجمالي كل الوقت
                $allTimeBilled    = Invoice::where('user_id', $dr->id)->sum('total_amount');
                $allTimeCollected = Invoice::where('user_id', $dr->id)->sum('paid_amount');

                return [
                    'id'              => $dr->id,
                    'name'            => $dr->name,
                    'specialty'       => $dr->doctor?->specialty ?? '—',
                    'commission'      => $commission,
                    'cases'           => $casesCount,
                    'total_billed'    => $totalBilled,
                    'total_collected' => $totalCollected,
                    'pending'         => $pending,
                    'dr_share'        => $drShare,
                    'appt_total'      => $apptTotal,
                    'appt_completed'  => $apptCompleted,
                    'appt_cancelled'  => $apptCancelled,
                    'all_billed'      => $allTimeBilled,
                    'all_collected'   => $allTimeCollected,
                ];
            });

        return view('reports.index', compact(
            'dailyIncome', 'totalIncome', 'newPatients',
            'returningPatients', 'pendingBalance', 'monthlyIncome',
            'month', 'doctors'
        ));
    }
}
