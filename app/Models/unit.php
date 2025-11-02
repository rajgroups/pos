<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enum\Status;

class unit extends Model
{
    use HasFactory;

    protected $table = 'tbl_unit';
    protected $fillable = [
        'name',
        'shortname',
        'no_of_product',
        'status'
    ];

    protected $cast = [
        'status'        => Status::class,
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
