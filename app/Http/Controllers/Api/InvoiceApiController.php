<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->isDoctor()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $query = Invoice::with(['patient:id,name,phone', 'items']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->whereColumn('paid_amount', '>=', 'total_amount');
            } elseif ($request->status === 'partial') {
                $query->where('paid_amount', '>', 0)->whereColumn('paid_amount', '<', 'total_amount');
            } elseif ($request->status === 'unpaid') {
                $query->where('paid_amount', 0);
            }
        }

        $invoices = $query->latest()->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $invoices->items(),
            'meta'    => [
                'total'        => $invoices->total(),
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($request->user()->isDoctor()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $invoice->load(['patient:id,name,phone,address', 'items', 'payments']);

        return response()->json([
            'success' => true,
            'data'    => array_merge($invoice->toArray(), [
                'remaining'  => $invoice->total_amount - $invoice->paid_amount,
                'status'     => $invoice->paid_amount >= $invoice->total_amount ? 'paid'
                    : ($invoice->paid_amount > 0 ? 'partial' : 'unpaid'),
            ]),
        ]);
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        if ($request->user()->isDoctor()) {
            return response()->json(['success' => false, 'message' => 'غير مسموح'], 403);
        }

        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,other',
            'payment_date'   => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $remaining = $invoice->total_amount - $invoice->paid_amount;
        if ($request->amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'المبلغ أكبر من المتبقي (' . $remaining . ' جنيه)',
            ], 422);
        }

        $payment = $invoice->payments()->create([
            'patient_id'     => $invoice->patient_id,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date'   => $request->payment_date ?? today()->toDateString(),
            'notes'          => $request->notes,
        ]);

        $invoice->increment('paid_amount', $request->amount);

        return response()->json([
            'success'   => true,
            'message'   => 'تم تسجيل الدفعة',
            'payment'   => $payment,
            'invoice'   => [
                'total_amount' => $invoice->total_amount,
                'paid_amount'  => $invoice->fresh()->paid_amount,
                'remaining'    => $invoice->total_amount - $invoice->fresh()->paid_amount,
            ],
        ]);
    }
}
