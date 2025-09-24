<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivitiessController extends Controller
{
    public function activities()
    {
        // Logic to fetch and display activities
        return view('activities');
    }
}
