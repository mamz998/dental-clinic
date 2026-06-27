<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id', 'patient_id', 'amount',
        'payment_method', 'payment_date', 'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected static function booted(): void
    {
        // بعد كل دفعة، حدّث المبلغ المدفوع في الفاتورة
        static::saved(function ($payment) {
            $invoice = $payment->invoice;
            $invoice->paid_amount = $invoice->payments()->sum('amount');
            $invoice->saveQuietly();
        });
    }
}
