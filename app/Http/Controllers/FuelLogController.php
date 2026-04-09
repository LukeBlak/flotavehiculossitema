<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Http\Requests\StoreFuelLogRequest;
use App\Http\Requests\UpdateFuelLogRequest;

class FuelLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['gerente', 'supervisor'])) {
            $fuelLogs = FuelLog::with('trip', 'vehicle', 'user')->get();
        } else {
            $fuelLogs = FuelLog::where('user_id', $user->id)->with('trip', 'vehicle', 'user')->get();
        }

        return response()->json(['data' => $fuelLogs]);
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
    public function store(StoreFuelLogRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['total_cost'] = $data['liters'] * $data['cost_per_liter'];
        
        $fuelLog = FuelLog::create($data);
        return response()->json(['data' => $fuelLog], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FuelLog $fuelLog)
    {
        $this->authorize('view', $fuelLog);
        $fuelLog->load('trip', 'vehicle', 'user');
        return response()->json(['data' => $fuelLog]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FuelLog $fuelLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFuelLogRequest $request, FuelLog $fuelLog)
    {
        $this->authorize('update', $fuelLog);
        $data = $request->validated();
        
        if (isset($data['liters']) && isset($data['cost_per_liter'])) {
            $data['total_cost'] = $data['liters'] * $data['cost_per_liter'];
        } elseif (isset($data['liters'])) {
            $data['total_cost'] = $data['liters'] * $fuelLog->cost_per_liter;
        } elseif (isset($data['cost_per_liter'])) {
            $data['total_cost'] = $fuelLog->liters * $data['cost_per_liter'];
        }

        $fuelLog->update($data);
        return response()->json(['data' => $fuelLog]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FuelLog $fuelLog)
    {
        $this->authorize('delete', $fuelLog);
        $fuelLog->delete();
        return response()->json(['message' => 'Registro de combustible eliminado'], 204);
    }

        /**
         * Aprobar un registro de combustible.
         */
        public function approve(FuelLog $fuelLog)
        {
            $this->authorize('update', $fuelLog);
            return response()->json(['data' => $fuelLog->fresh(), 'message' => 'Combustible aprobado']);
        }
}
