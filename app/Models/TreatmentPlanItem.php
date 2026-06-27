<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanItem extends Model
{
    protected $fillable = [
        'treatment_plan_id', 'tooth_id', 'treatment_type',
        'description', 'cost', 'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'planned'     => 'مخطط',
            'in_progress' => 'جاري',
            'done'        => 'مكتمل',
            default       => $this->status,
        };
    }
}
