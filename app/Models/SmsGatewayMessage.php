<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsGatewayMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_device_id',
        'to',
        'message',
        'status',
        'idempotency_key',
        'attempts',
        'expires_at',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(SmsGatewayDevice::class, 'gateway_device_id');
    }
}
