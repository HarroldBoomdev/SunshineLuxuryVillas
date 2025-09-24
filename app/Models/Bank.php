<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    protected $fillable = ['reference','address','name','telephone','mobile'];

    public function links(): HasMany
    {
        return $this->hasMany(BankProperty::class);
    }
}
