<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // show the add property wizard form
    public function create()
    {
        return view('forms.new-property');
    }

    // handle form submit later
    public function store(Request $request)
    {
        dd($request->all()); // temporary preview of submitted data
    }
}
