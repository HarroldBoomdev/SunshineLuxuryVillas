<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryModel extends Model
{
    use HasFactory;

    protected $table = 'diary_entries';

    protected $fillable = [
        'type',
        'date',
        'time',
        'duration',
        'participants',
        'lead_source',
        'notes',
        'linked_to',
        'color',
        'is_done',
    ];
}
