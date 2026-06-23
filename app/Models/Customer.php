<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'is_verified',
        'otp',
        'otp_expires_at',
    ];

    protected $casts = [
        'is_verified'    => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    protected $hidden = ['otp'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: 'Customer';
    }

    public function isOtpValid(string $otp): bool
    {
        return $this->otp === $otp
            && $this->otp_expires_at
            && $this->otp_expires_at->isFuture();
    }

    public function isRegistrationComplete(): bool
    {
        return $this->first_name && $this->last_name
            && ($this->email || $this->phone_number);
    }

    // Route notifications to the right email address
    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }
}
