<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientModel;
use App\Models\PropertiesModel;
use App\Models\MatchModel;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;


class ClientsController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(new ClientsExport($request), 'clients.xlsx');
    }

    public function index(Request $request)
    {
        $query = ClientModel::query();

        // Search: First Name
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }

        // Search: Last Name
        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        }

        // Search: Email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Search: Status
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // ✅ Price Range Filtering
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('MinimumPrice', '>=', $request->min_price)
                ->orWhere('MaximumPrice', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price') && $request->max_price < 1000000) {
            $query->where(function ($q) use ($request) {
                $q->where('MaximumPrice', '<=', $request->max_price)
                ->orWhere('MinimumPrice', '<=', $request->max_price);
            });
        }

        $clients = $query->paginate(20)->appends($request->query());

        return view('clients.index', compact('clients'));
    }

    public function edit($id)
    {
        $client = ClientModel::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
{
    $client = ClientModel::findOrFail($id);

    $validatedData = $request->validate([
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'mobile' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'idcardnum' => 'nullable|string|max:50',
        'refAgentCon' => 'nullable|string|max:255',
        'prefLang' => 'nullable|string|max:50',
        'dob' => 'nullable|date',
        'zipcode' => 'nullable|string|max:20',
        'region' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'fax' => 'nullable|string|max:20',
        'nationality' => 'nullable|string|max:100',
        'passportNum' => 'nullable|string|max:50',
        'refAgent' => 'nullable|string|max:255',
        'leadSource' => 'nullable|string|max:255',
        'branch' => 'nullable|string|max:255',
        'subStatus' => 'nullable|string|max:20',
        'notes' => 'nullable|string',
        'bedroooms' => 'nullable|integer',
        'bathrooms' => 'nullable|integer',
        'pool' => 'nullable|string|max:255',
        'specifications' => 'nullable|string|max:255',
        'orientation' => 'nullable|string|max:255',
        'coveredM' => 'nullable|string|max:255',
        'constructstart' => 'nullable|integer',
        'floor' => 'nullable|string|max:255',
        'purchaseMin' => 'nullable|integer',
        'plotM' => 'nullable|string|max:255',
        'parking' => 'nullable|string|max:255',
        'reasonsForBuying' => 'nullable|string|max:255',
        'timeframe' => 'nullable|string|max:255',
        'matchingSystem' => 'nullable|boolean',
    ]);

    $client->update($validatedData);

    return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
}


    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('forms.new-client-form');
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'idcardnum' => 'nullable|string|max:50',
            'refAgentCon' => 'nullable|string|max:255',
            'prefLang' => 'nullable|string|max:50',
            'dob' => 'nullable|date',
            'zipcode' => 'nullable|string|max:20',
            'region' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'fax' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'passportNum' => 'nullable|string|max:50',
            'refAgent' => 'nullable|string|max:255',
            'leadSource' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'subStatus' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'bedroooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'pool' => 'nullable|string|max:255',
            'specifications' => 'nullable|string|max:255',
            'orientation' => 'nullable|string|max:255',
            'coveredM' => 'nullable|string|max:255',
            'constructstart' => 'nullable|integer',
            'floor' => 'nullable|string|max:255',
            'purchaseMin' => 'nullable|integer',
            'plotM' => 'nullable|string|max:255',
            'parking' => 'nullable|string|max:255',
            'reasonsForBuying' => 'nullable|string|max:255',
            'timeframe' => 'nullable|string|max:255',
            'matchingSystem' => 'nullable|boolean',
        ]);

        $validatedData['first_name'] = $validatedData['fname'];
        $validatedData['last_name'] = $validatedData['lname'];
        unset($validatedData['fname'], $validatedData['lname']);

        ClientModel::create($validatedData);

        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }


    public function show($id)
    {
        // Fetch the client details
        $client = ClientModel::findOrFail($id);

        // Fetch matches based on client preferences
        $matches = PropertiesModel::query();

        // Add price matching if the client has a budget
        if (!empty($client->min_budget) && !empty($client->max_budget)) {
            $matches->where('price', '>=', $client->min_budget)
                    ->where('price', '<=', $client->max_budget);
        }

        // Add location matching if preferred_location exists
        if (!empty($client->preferred_location)) {
            $matches->where('location', $client->preferred_location);
        }

        // Get the matched properties
        $matches = $matches->get();

        // Pass both client and matches to the view
        return view('clients.details', compact('client', 'matches'));
    }

    public function updateMatchStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        // Fetch the match record
        $match = MatchModel::findOrFail($id);
        $match->status = $request->status; // Update the status
        $match->save(); // Save the changes

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function destroy($id)
    {
        $client = ClientModel::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');
    }

    public function search(Request $request)
    {
        $term = $request->input('q');
        $type = $request->input('type');

        if ($type === 'client') {
            $clients = ClientModel::where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->limit(10)
                ->get();

            return response()->json(
                $clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'text' => $client->first_name . ' ' . $client->last_name
                    ];
                })
            );
        }

        if ($type === 'property') {
            $properties = PropertiesModel::where('reference', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")
                ->limit(10)
                ->get();

            return response()->json(
                $properties->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'text' => "{$property->reference} – {$property->title}"
                    ];
                })
            );
        }

        return response()->json([]); // default fallback if type is missing or invalid
    }




}
