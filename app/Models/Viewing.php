<?php

namespace App\Models;
use App\Models\User;
use App\Models\PropertiesModel;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viewing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'title',
        'activity_type',
        'viewing_date',
        'duration',
        'client_name',
        'client_email',
        'client_phone',
        'client_whatsapp',
        'notes',
        'linked_to',
        'is_done',
        'property_id',
        'client_ids',       // ✅ Add this
        'property_ids',     // ✅ Add this
    ];

    protected $casts = [
        'client_ids' => 'array',
        'property_ids' => 'array',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function property()
    {
        return $this->belongsTo(PropertiesModel::class, 'property_id');
    }
}
