<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function create()
    {
        // Fetch countries from an API
        $response = Http::get('https://restcountries.com/v3.1/all');
        $countries = collect($response->json())->sortBy('name.common')->pluck('name.common');

        return view('form', compact('countries'));
    }
}
