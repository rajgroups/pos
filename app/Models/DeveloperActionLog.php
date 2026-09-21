<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_user_id',
        'admin_name',
        'action',
        'command',
        'status',
        'output',
        'error_output',
        'ip_address',
        'user_agent',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_user_id');
    }
}
