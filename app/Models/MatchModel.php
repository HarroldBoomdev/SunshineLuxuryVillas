<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches'; // Ensure it matches the database table name

    protected $fillable = [
        'property_id',
        'client_id',
        'thumbnail',
        'area',
        'price',
        'status'
    ]; // Add other columns as needed
}
