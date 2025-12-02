<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PropertiesModel;
use App\Models\User;

class PropertyActivityLog extends Model
{
    protected $fillable = [
        'property_id', 'user_id', 'action', 'changes'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public function property()
    {
        return $this->belongsTo(PropertiesModel::class, 'property_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
