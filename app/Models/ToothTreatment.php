<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToothTreatment extends Model
{
    protected $fillable = [
        'patient_id', 'tooth_id', 'user_id',
        'treatment_type', 'description', 'treatment_date', 'cost',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'cost'           => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
