<?php

namespace App\Http\Controllers;

use App\Models\Viewing;
use App\Models\User;
use App\Models\PropertiesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewingController extends Controller
{
    // Constructor to apply authentication
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show the form to create a new viewing
    public function create()
    {
        $properties = PropertiesModel::all(); // Get all properties
        $users = User::whereIn('is_admin', [1, 0])->get(); // Get users (admins and non-admins)

        return view('viewing.create', compact('properties', 'users')); // Pass properties and users to view
    }

    // Store the new viewing data
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'viewing_date' => 'required|date',
            'viewing_time' => 'required|date_format:H:i',
            'assigned_to' => 'required|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_phone' => 'required|string|max:15',
            'client_whatsapp' => 'nullable|string|max:15',
            'property_id' => 'required|exists:properties,id',
        ]);

        try {
            // Create the viewing entry
            $viewing = Viewing::create([
                'user_id' => Auth::id(), // The user who created the viewing
                'assigned_to' => $request->assigned_to, // The agent assigned to the viewing
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'client_whatsapp' => $request->client_whatsapp,
                'property_id' => $request->property_id,
                'viewing_date' => $request->viewing_date,
                'viewing_time' => $request->viewing_time,
            ]);

            // Redirect to the viewings index page with a success message
            return redirect()->route('admin.viewings.index')->with('success', 'Viewing scheduled successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and return an error message
            return back()->with('error', 'There was an error scheduling the viewing. Please try again.');
        }
    }

    // Display all viewings (Admin can see all)
    public function index()
    {
        // Get all viewings
        $viewings = Viewing::all();
        return view('admin.viewing.index', compact('viewings')); // Pass viewings to the view
    }

    // Show details of a specific viewing
    public function show(Viewing $viewing)
    {
        return view('admin.viewing.show', compact('viewing')); // Display specific viewing details
    }

    // Show the form to edit a viewing
    public function edit(Viewing $viewing)
    {
        $properties = PropertiesModel::all(); // Get all properties
        $agents = User::role('Agent')->get(); // Get all agents (with Agent role)
        return view('admin.viewing.edit', compact('viewing', 'properties', 'agents')); // Pass viewing, properties, and agents to view
    }

    // Update a specific viewing
    public function update(Request $request, Viewing $viewing)
    {
        // Validate the incoming request data
        $request->validate([
            'viewing_date' => 'required|date',
            'viewing_time' => 'required|date_format:H:i',
            'assigned_to' => 'required|exists:users,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_phone' => 'required|string|max:15',
            'client_whatsapp' => 'nullable|string|max:15',
            'property_id' => 'required|exists:properties,id',
        ]);

        try {
            // Update the viewing entry
            $viewing->update([
                'assigned_to' => $request->assigned_to,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'client_whatsapp' => $request->client_whatsapp,
                'property_id' => $request->property_id,
                'viewing_date' => $request->viewing_date,
                'viewing_time' => $request->viewing_time,
            ]);

            // Redirect to the viewings index page with a success message
            return redirect()->route('admin.viewing.index')->with('success', 'Viewing updated successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and return an error message
            return back()->with('error', 'There was an error updating the viewing. Please try again.');
        }
    }

    // Delete a specific viewing
    public function destroy(Viewing $viewing)
    {
        try {
            // Delete the viewing entry
            $viewing->delete();

            // Redirect to the viewings index page with a success message
            return redirect()->route('admin.viewing.index')->with('success', 'Viewing deleted successfully!');
        } catch (\Exception $e) {
            // Handle any exceptions and return an error message
            return back()->with('error', 'There was an error deleting the viewing. Please try again.');
        }
    }
}
