<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'phone'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // الأدوار
    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isDoctor(): bool      { return $this->role === 'doctor'; }
    public function isReceptionist(): bool{ return $this->role === 'receptionist'; }

    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin'        => 'مدير',
            'doctor'       => 'دكتور',
            'receptionist' => 'استقبال',
            default        => $this->role,
        };
    }

    // العلاقات
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
}
