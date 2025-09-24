<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchModel;
use App\Models\PropertiesModel; // Include Properties
use App\Models\ClientModel; // Include Clients

class MatchesController extends Controller
{
    public function index()
    {
        // Fetch all matches by retrieving properties that match any client’s preferences
        $matches = PropertiesModel::query();

        // Retrieve all clients
        $clients = ClientModel::all();

        // Apply filtering based on client preferences
        foreach ($clients as $client) {
            if (!empty($client->min_budget) && !empty($client->max_budget)) {
                $matches->orWhereBetween('price', [$client->min_budget, $client->max_budget]);
            }
            if (!empty($client->preferred_location)) {
                $matches->orWhere('location', $client->preferred_location);
            }
        }

        // Paginate results to show 20 per page
        $matches = $matches->paginate(20);

        // Pass the filtered matches to the view
        return view('matches.index', compact('matches'));
    }
}

