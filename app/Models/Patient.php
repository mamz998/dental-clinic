<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'phone_alt', 'date_of_birth', 'gender',
        'national_id', 'address', 'emergency_contact_name',
        'emergency_contact_phone', 'photo', 'referral_source', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function medicalHistory(): HasOne
    {
        return $this->hasOne(MedicalHistory::class);
    }

    public function teeth(): HasMany
    {
        return $this->hasMany(Tooth::class);
    }

    public function toothTreatments(): HasMany
    {
        return $this->hasMany(ToothTreatment::class);
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(PatientFile::class);
    }

    public function getBalanceAttribute(): float
    {
        return $this->invoices()->sum('total_amount') - $this->invoices()->sum('paid_amount');
    }

    public function hasRisk(): bool
    {
        $history = $this->medicalHistory;
        if (!$history) return false;

        return $history->allergy_anesthesia
            || $history->allergy_penicillin
            || $history->has_diabetes
            || $history->has_heart_disease
            || $history->has_bleeding_disorder
            || $history->is_pregnant;
    }
}
