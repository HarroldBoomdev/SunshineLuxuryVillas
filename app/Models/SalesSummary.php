<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesSummary extends Model
{
    use HasFactory;

    protected $table = 'sales_summary';

    protected $fillable = [
        'year',
        'total_sales',
        'total_value',
        'total_commission',
        'avg_price',
        'avg_commission',
        'total_leads',
        'conversion_rate',
    ];
}
