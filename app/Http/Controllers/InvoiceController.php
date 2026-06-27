<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('patient')->latest('invoice_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->whereColumn('paid_amount', '>=', 'total_amount');
            } elseif ($request->status === 'unpaid') {
                $query->whereColumn('paid_amount', '<', 'total_amount');
            }
        }

        $invoices = $query->paginate(20)->withQueryString();

        $totalPending = Invoice::selectRaw('SUM(total_amount - paid_amount) as bal')->value('bal') ?? 0;

        return view('invoices.index', compact('invoices', 'totalPending'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name', 'phone']);
        $selectedPatient = $request->patient_id ? Patient::find($request->patient_id) : null;
        return view('invoices.create', compact('patients', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'invoice_date'          => 'required|date',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'patient_id'   => $request->patient_id,
            'user_id'      => auth()->id(),
            'invoice_date' => $request->invoice_date,
            'notes'        => $request->notes,
            'total_amount' => 0,
            'paid_amount'  => 0,
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $itemTotal,
            ]);
            $total += $itemTotal;
        }

        $invoice->update(['total_amount' => $total]);

        // Record initial payment if provided
        if ($request->filled('initial_payment') && $request->initial_payment > 0) {
            Payment::create([
                'invoice_id'     => $invoice->id,
                'patient_id'     => $invoice->patient_id,
                'amount'         => $request->initial_payment,
                'payment_method' => $request->payment_method ?? 'cash',
                'payment_date'   => $request->invoice_date,
            ]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('patient', 'items', 'payments', 'doctor');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name']);
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'patients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_date'          => 'required|date',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.description'   => 'required|string',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        $invoice->items()->delete();

        $total = 0;
        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $itemTotal,
            ]);
            $total += $itemTotal;
        }

        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'notes'        => $request->notes,
            'total_amount' => $total,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'تم تحديث الفاتورة');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة');
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . $invoice->remaining,
            'payment_method' => 'required|in:cash,card,other',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        Payment::create([
            'invoice_id'     => $invoice->id,
            'patient_id'     => $invoice->patient_id,
            'amount'         => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date'   => $request->payment_date,
            'notes'          => $request->notes,
        ]);

        return back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }
}
