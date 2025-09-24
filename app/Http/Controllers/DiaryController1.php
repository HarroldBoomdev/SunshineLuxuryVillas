<?php

namespace App\Http\Controllers;

use App\Models\DiaryModel; 
use App\Models\PropertiesModel; 
use App\Models\TenantModel; 
use App\Models\Deal; 
use Illuminate\Http\Request;

class DiaryController extends Controller
{
    public function index()
    {
        $property = PropertiesModel::first();
        $diaries = DiaryModel::all();
        $deals = Deal::all(); // Correctly named as 'deals'

        // Pass all variables to the view
        return view('properties.index', compact('property', 'diaries', 'deals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'nullable|string',
            'participants' => 'nullable|string',
            'lead_source' => 'nullable|string',
            'notes' => 'nullable|string',
            'linked_to' => 'nullable|string',
            'color' => 'nullable|string',
            'is_done' => 'nullable|boolean',
        ]);

        DiaryModel::create($request->all());

        return redirect()->back()->with('success', 'Diary entry created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'nullable|string',
            'participants' => 'nullable|string',
            'lead_source' => 'nullable|string',
            'notes' => 'nullable|string',
            'linked_to' => 'nullable|string',
            'color' => 'nullable|string',
            'is_done' => 'nullable|boolean',
        ]);

        $diary = DiaryModel::findOrFail($id);
        $diary->update($request->all());

        return redirect()->back()->with('success', 'Diary entry updated successfully.');
    }

    public function destroy($id)
    {
        $diary = DiaryModel::findOrFail($id);
        $diary->delete();

        return redirect()->back()->with('success', 'Diary entry deleted successfully.');
    }
}
