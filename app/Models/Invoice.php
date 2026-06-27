<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'patient_id', 'user_id', 'invoice_number',
        'total_amount', 'paid_amount', 'notes', 'invoice_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->paid_amount >= $this->total_amount;
    }

    protected static function booted(): void
    {
        static::creating(function ($invoice) {
            $invoice->invoice_number = 'INV-' . date('Y') . '-' . str_pad(
                Invoice::whereYear('created_at', date('Y'))->count() + 1,
                4, '0', STR_PAD_LEFT
            );
        });
    }
}
