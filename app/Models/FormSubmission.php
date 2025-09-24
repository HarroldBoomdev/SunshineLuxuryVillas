<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
    'form_key', 'type', 'name', 'email', 'phone', 'reference', 'payload',
    ];

    protected $casts = [
    'payload' => 'array',
    ];
}
