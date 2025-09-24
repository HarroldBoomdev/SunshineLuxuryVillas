<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    use HasFactory;
    protected $table = 'clients';

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'mobile',
        'email',
        'id_card_number',
        'labels',
        'referral_agent_contact',
        'preferred_language',
        'managing_agent',
        'dob',
        'zip_code',
        'region',
        'phone',
        'fax',
        'nationality',
        'passport_number',
        'referral_agent',
        'lead_source',
        'branch',
        'subscription_status',
        'notes',
        'buyer_profile',
        'bedrooms',
        'bathrooms',
        'pool',
        'specifications',
        'orientation',
        'covered_area',
        'construction_year',
        'floor',
        'purchase_budget',
        'furnished',
        'plot_area',
        'parking',
        'reasons_for_buying',
        'time_frame',
        'matching_system',
        'contacts',
        'notifications',
    ];
    public $timestamps = true;

}
