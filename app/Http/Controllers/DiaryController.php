<?php

namespace App\Http\Controllers;

use App\Models\Viewing;
use App\Models\User;
use App\Models\ClientModel;
use App\Models\PropertiesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        $query = Viewing::with(['property', 'assignedTo']);

        if (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhere('assigned_to', $user->id);
            });
        }

        $viewings = $query->get();

        $events = $viewings->map(function ($viewing) {
            $color = match ($viewing->activity_type) {
                'Viewing' => 'blue',
                'Take On' => 'green',
                'Misc' => 'orange',
                default => 'gray'
            };

            return [
                'title' => $viewing->title, // ✅ Keep just the title
                'start' => $viewing->viewing_date,
                'end' => $viewing->end_time,
                'color' => $color,
                'url' => route('diary.edit', $viewing->id),
            ];
        });

        $users = User::all();

        return view('diary.index', compact('viewings', 'events', 'users'));
    }

    public function store(Request $request)
    {
        $start = Carbon::parse($request->date . ' ' . $request->start_time);
        $end = Carbon::parse($request->date . ' ' . $request->end_time);
        $duration = $end->diffInMinutes($start);

        $clientIds = $request->input('clients', []);
        $propertyIds = $request->input('properties', []);

        \Log::info('Client IDs:', $clientIds);
        \Log::info('Property IDs:', $propertyIds);

        $clients = ClientModel::whereIn('id', $clientIds)->get();
        $properties = PropertiesModel::whereIn('id', $propertyIds)->get();

        $clientName = $clients->map(fn($c) => trim("{$c->first_name} {$c->last_name}"))->implode(', ');
        $clientEmail = $clients->pluck('email')->implode(', ');
        $clientPhone = $clients->map(fn($c) => $c->phone ?: $c->mobile ?: '—')->implode(', ');

        $propertyTitles = $properties->map(fn($p) => "{$p->reference} - {$p->title}")->implode(', ');

        Viewing::create([
            'user_id'       => auth()->id(),
            'assigned_to'   => $request->assigned_to,
            'title'         => $request->title,
            'activity_type' => $request->activity_type,
            'viewing_date'  => $start,
            'duration'      => $duration,
            'client_name'   => $clientName,
            'client_email'  => $clientEmail,
            'client_phone'  => $clientPhone,
            'client_ids'    => json_encode(array_values($clientIds)),
            'property_ids'  => json_encode(array_values($propertyIds)),
            'notes'         => $request->notes,
            'is_done'       => $request->has('mark_done'),
        ]);

        return redirect()->route('diary.index')->with('success', 'Viewing scheduled successfully.');
    }

    public function edit($id)
    {
        $diary = Viewing::findOrFail($id);
        $users = User::all();

        return view('diary.edit', compact('diary', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'activity_type' => 'required|in:Viewing,Take On,Misc',
        ]);

        $viewing = Viewing::findOrFail($id);

        $start = Carbon::parse($request->date . ' ' . $request->start_time);
        $end = Carbon::parse($request->date . ' ' . $request->end_time);
        $duration = $end->diffInMinutes($start);

        $viewing->update([
            'title' => $request->title,
            'viewing_date' => $start,
            'duration' => $duration . ' mins',
            'assigned_to' => $request->assigned_to,
            'activity_type' => $request->activity_type,
            'notes' => $request->notes,
            'property_id' => $request->property_id ?? null,
            'is_done' => $request->has('mark_done'),
        ]);

        return redirect()->route('diary.index')->with('success', 'Activity updated successfully!');
    }

    public function destroy($id)
    {
        $viewing = Viewing::findOrFail($id);
        $viewing->delete(); // Just delete the diary/event, nothing else

        return redirect()->route('diary.index')->with('success', 'Diary event deleted successfully. Client data is untouched.');
    }

}
