<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DeveloperModel;
use Illuminate\Http\Request;
use App\Exports\DevelopersExport;
use Maatwebsite\Excel\Facades\Excel;

class DeveloperController extends Controller
{
    public function export(Request $request)
    {
        $filters = $request->only('reference', 'name', 'email');
        return Excel::download(new DevelopersExport($filters), 'developers.xlsx');
    }

    public function index(Request $request)
    {
        $query = DeveloperModel::query();

        // Filter by Reference
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        // Filter by Name (first_name or last_name)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by Email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Paginate results
        $developers = $query->paginate(10)->appends($request->query());

        return view('developers.index', compact('developers'));
    }

    public function create()
    {
        return view('forms.new-developer-form');
    }

    // Handle form submission
    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:developers,email',
            'phone' => 'required|string|max:255',
            'website' => 'required|string|max:255',
        ]);

        DeveloperModel::create($request->all());

        return redirect()->route('developers.index')->with('success', 'Developer added successfully!');
    }

    public function show($id)
    {
        $developer = DeveloperModel::findOrFail($id);
        return view('developers.details', compact('developer'));
    }

    // Show the form to edit a developer
    public function edit($id)
    {
        $developer = DeveloperModel::findOrFail($id);
        return view('developers.edit', compact('developer'));
    }

    // Update a developer
    public function update(Request $request, $id)
    {
        $developer = DeveloperModel::findOrFail($id);

        $request->validate([
            'reference' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:developers,email,' . $id,
            'phone' => 'required|string|max:255',
            'website' => 'required|string|max:255',
        ]);

        $developer->update($request->all());

        return redirect()->route('developers.index')->with('success', 'Developer updated successfully!');
    }

    // Delete a developer
    public function destroy($id)
    {
        $developer = DeveloperModel::findOrFail($id);
        $developer->delete();

        return redirect()->route('developers.index')->with('success', 'Developer deleted successfully!');
    }
}