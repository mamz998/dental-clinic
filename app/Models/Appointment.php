<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'user_id', 'starts_at', 'ends_at',
        'title', 'notes', 'status', 'reminder_sent',
    ];

    protected $casts = [
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'scheduled'  => 'محدد',
            'confirmed'  => 'مؤكد',
            'completed'  => 'مكتمل',
            'cancelled'  => 'ملغي',
            'no_show'    => 'لم يحضر',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled'  => 'blue',
            'confirmed'  => 'green',
            'completed'  => 'gray',
            'cancelled'  => 'red',
            'no_show'    => 'yellow',
            default      => 'gray',
        };
    }
}
