<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankProperty extends Model
{
    protected $table = 'bank_property';
    protected $fillable = ['bank_id','property_reference'];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
