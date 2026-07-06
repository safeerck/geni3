<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerOtp extends Model
{
    protected $fillable = ['customer_id', 'code', 'type', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    protected $hidden = ['code'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isActive(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    public function isValid(string $code): bool
    {
        return $this->code === $code && $this->isActive();
    }
}
