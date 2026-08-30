<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsGatewayDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'device_identifier',
        'phone_number',
        'status',
        'token_hash',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(SmsGatewayMessage::class, 'gateway_device_id');
    }
}
