<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_name',
        'amount',
        'pipeline',
        'stage',
        'branch',
        'assigned_to',
        'expected_close_date',
        'user_id', // Make sure this is included if you're saving it
    ];

    /**
     * The user who created this deal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
