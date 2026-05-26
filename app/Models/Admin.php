<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'profile_image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
