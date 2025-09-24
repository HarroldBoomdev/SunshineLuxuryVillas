<?php

namespace App\Http\Controllers;
use App\Models\AgentsModel;
use Illuminate\Http\Request;

class AgentsController extends Controller
{

    public function index(Request $request)
    {
        $query = AgentsModel::query();

        // Filter by reference
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }

        // Filter by name (first or last)
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->name . '%')
                ->orWhere('last_name', 'like', '%' . $request->name . '%');
            });
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Paginate results
        $agents = $query->paginate(10)->appends($request->query()); // Keep filters on pagination links

        return view('agents.index', compact('agents'));
    }


    public function show($id)
    {
        $agent = AgentsModel::findOrFail($id);
        return view('agents.details', compact('agent'));
    }

    public function create()
    {
        return view('forms.new-agent-form');
    }

    public function store(Request $request)
{
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'labels' => 'nullable|string', // Labels can be optional
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'nullable|string|max:20', // Optional mobile field
            'phone' => 'nullable|string|max:20',
            'website' => 'required|string|max:255',
            'subscription_status' => 'nullable|string|max:255', // Optional subscription status
        ]);

        // If labels are provided as a comma-separated string, convert them to JSON
        if (!empty($validated['labels'])) {
            $validated['labels'] = json_encode(array_map('trim', explode(',', $validated['labels'])));
        }

        AgentsModel::create($validated);

        return redirect()->route('agents.index')->with('success', 'Agent created successfully!');
    }


    public function edit($id)
    {
        $agent = AgentsModel::findOrFail($id);
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $agent = AgentsModel::findOrFail($id); // Fetch the agent by ID

        $validatedData = $request->validate([
            'reference' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'subscription_status' => 'nullable|string|max:255',
            'labels' => 'nullable|string|max:255',
        ]);

        $agent->update($validatedData); // Update the agent with validated data

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }



    public function destroy($id)
    {
        $agent = AgentsModel::findOrFail($id);
        $agent->delete();

        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully!');
    }
}
