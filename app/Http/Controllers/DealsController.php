<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PropertiesModel;
use App\Models\TenantModel;
use Illuminate\Http\Request;

class DealsController extends Controller
{

    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            $deals = Deal::with('user')->get();  // Admin sees all deals
        } else {
            $deals = Deal::where('user_id', auth()->id())->with('user')->get();  // Users only see their own deals
        }
        $property = PropertiesModel::first();
        $properties = PropertiesModel::all();

        return view('deals.index', compact('deals', 'property', 'properties'));
    }



    public function updateStage(Request $request) {
        $request->validate([
            'deal_id' => 'required|integer',
            'stage' => 'required|string'
        ]);

        $deal = Deal::find($request->deal_id);
        if ($deal) {
            $deal->stage = $request->stage;
            $deal->save();
            return response()->json(['status' => 'success', 'message' => 'Deal stage updated.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Deal not found.'], 404);
    }

    public function show($id)
    {
        $deal = Deal::find($id);

        if (!$deal) {
            return response()->json(['success' => false, 'message' => 'Deal not found'], 404);
        }

        return response()->json(['success' => true, 'deal' => $deal]);
    }

    public function store(Request $request)
    {
        \Log::info('Store method called');
        \Log::info($request->all());

        $validated = $request->validate([
            'title' => 'required|string',
            'client_name' => 'required|string',
            'amount' => 'required|numeric',
            'stage' => 'required|string',
            'pipeline' => 'nullable|string',
            'branch' => 'nullable|string',
            'assigned_to' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
        ]);

        try {
            // Use 'amount' directly without mapping to 'value'
            $deal = Deal::create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));

            \Log::info('Deal created successfully', ['deal' => $deal]);

            return response()->json(['success' => true, 'deal' => $deal]);
        } catch (\Exception $e) {
            \Log::error('Failed to create deal: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create deal.'], 500);
        }

    }



    public function update(Request $request, Deal $deal)
    {
        $request->validate([
            'title' => 'required',
            'client_name' => 'required',
            'amount' => 'required|numeric',
            'pipeline' => 'required',
            'stage' => 'required',
            'branch' => 'required',
            'assigned_to' => 'required',
            'expected_close_date' => 'required',
        ]);

        $deal->update($request->all());
        return response()->json(['message' => 'Deal updated successfully!']);
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();
        return response()->json(['message' => 'Deal deleted successfully!']);
    }
}
