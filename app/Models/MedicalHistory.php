<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    protected $fillable = [
        'patient_id', 'allergy_anesthesia', 'allergy_penicillin',
        'allergies_other', 'has_diabetes', 'has_heart_disease',
        'has_bleeding_disorder', 'chronic_conditions_other',
        'current_medications', 'is_pregnant', 'medical_notes',
    ];

    protected $casts = [
        'allergy_anesthesia'    => 'boolean',
        'allergy_penicillin'    => 'boolean',
        'has_diabetes'          => 'boolean',
        'has_heart_disease'     => 'boolean',
        'has_bleeding_disorder' => 'boolean',
        'is_pregnant'           => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function hasAnyRisk(): bool
    {
        return $this->allergy_anesthesia
            || $this->allergy_penicillin
            || $this->has_diabetes
            || $this->has_heart_disease
            || $this->has_bleeding_disorder
            || $this->is_pregnant;
    }
}
