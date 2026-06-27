<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tooth extends Model
{
    protected $fillable = [
        'patient_id', 'tooth_number', 'tooth_type', 'status', 'notes',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(ToothTreatment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'healthy'          => 'سليم',
            'filling'          => 'حشو',
            'crown'            => 'تلبيسة',
            'root_canal'       => 'علاج عصب',
            'missing'          => 'مفقود',
            'needs_extraction' => 'يحتاج خلع',
            'implant'          => 'زراعة',
            'bridge'           => 'جسر',
            default            => 'أخرى',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'healthy'          => '#22c55e',
            'filling'          => '#3b82f6',
            'crown'            => '#a855f7',
            'root_canal'       => '#f97316',
            'missing'          => '#6b7280',
            'needs_extraction' => '#ef4444',
            'implant'          => '#06b6d4',
            'bridge'           => '#8b5cf6',
            default            => '#d1d5db',
        };
    }
}
