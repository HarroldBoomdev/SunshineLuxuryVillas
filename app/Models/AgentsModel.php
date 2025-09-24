<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentsModel extends Model
{
    use HasFactory;
    protected $table = 'agents';

    protected $fillable = [
        'reference',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'phone',
        'website',
        'subscription_status',
        'labels',
    ];
    
}
