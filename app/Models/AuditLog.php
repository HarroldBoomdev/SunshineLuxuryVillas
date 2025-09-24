<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'trace_id',
        'type',
        'resource_action',
        'user_name',
        'user_image',
        'ip_address',
        'date_time',
    ];
}
