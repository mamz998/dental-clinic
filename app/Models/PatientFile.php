<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PatientFile extends Model
{
    protected $fillable = [
        'patient_id', 'user_id', 'file_name', 'file_path',
        'file_type', 'mime_type', 'file_size', 'description', 'taken_date',
    ];

    protected $casts = [
        'taken_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->file_type) {
            'xray'     => '🦷',
            'photo'    => '📷',
            'document' => '📄',
            default    => '📎',
        };
    }
}
