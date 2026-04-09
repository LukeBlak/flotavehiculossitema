<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['gerente', 'supervisor'])) {
            $incidents = Incident::with('trip', 'vehicle', 'user')->get();
        } else {
            $incidents = Incident::where('user_id', $user->id)->with('trip', 'vehicle', 'user')->get();
        }

        return response()->json(['data' => $incidents]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        $incident = Incident::create($data);
        return response()->json(['data' => $incident], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        $this->authorize('view', $incident);
        $incident->load('trip', 'vehicle', 'user');
        return response()->json(['data' => $incident]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        $this->authorize('update', $incident);
        $incident->update($request->validated());
        return response()->json(['data' => $incident]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);
        $incident->delete();
        return response()->json(['message' => 'Incidente eliminado'], 204);
    }

        /**
         * Resolver un incidente.
         */
        public function resolve(Request $request, Incident $incident)
        {
            $this->authorize('update', $incident);
            $incident->update([
                'status' => 'resolved',
                'resolution' => $request->input('resolution'),
            ]);

            if ($incident->trip && ! in_array($incident->trip->status, ['completed', 'cancelled'], true)) {
                $incident->trip->update(['status' => 'in_progress']);
            }

            return response()->json(['data' => $incident, 'message' => 'Incidente resuelto']);
        }

}
