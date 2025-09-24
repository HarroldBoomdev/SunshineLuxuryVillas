<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'trace_id', 
        'type', 
        'url', 
        'resource', 
        'user_name', 
        'user_image', 
        'ip_address', 
        'operating_system', 
        'date_time',
    ];
}
