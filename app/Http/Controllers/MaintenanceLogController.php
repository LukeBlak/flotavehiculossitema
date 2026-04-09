<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Http\Requests\StoreMaintenanceLogRequest;
use App\Http\Requests\UpdateMaintenanceLogRequest;

class MaintenanceLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenanceLogs = MaintenanceLog::with('vehicle', 'user')->get();
        return response()->json(['data' => $maintenanceLogs]);
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
    public function store(StoreMaintenanceLogRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        
        $maintenanceLog = MaintenanceLog::create($data);
        return response()->json(['data' => $maintenanceLog], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceLog $maintenanceLog)
    {
        $this->authorize('view', $maintenanceLog);
        $maintenanceLog->load('vehicle', 'user');
        return response()->json(['data' => $maintenanceLog]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceLog $maintenanceLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaintenanceLogRequest $request, MaintenanceLog $maintenanceLog)
    {
        $this->authorize('update', $maintenanceLog);
        $maintenanceLog->update($request->validated());
        return response()->json(['data' => $maintenanceLog]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceLog $maintenanceLog)
    {
        $this->authorize('delete', $maintenanceLog);
        $maintenanceLog->delete();
        return response()->json(['message' => 'Registro de mantenimiento eliminado'], 204);
    }

        /**
         * Aprobar un registro de mantenimiento.
         */
        public function approve(MaintenanceLog $maintenanceLog)
        {
            $this->authorize('approve', $maintenanceLog);
            $maintenanceLog->update(['status' => 'approved']);
            return response()->json(['data' => $maintenanceLog, 'message' => 'Mantenimiento aprobado']);
        }

        /**
         * Rechazar un registro de mantenimiento.
         */
        public function reject(MaintenanceLog $maintenanceLog)
        {
            $this->authorize('approve', $maintenanceLog);
            $maintenanceLog->update(['status' => 'rejected']);
            return response()->json(['data' => $maintenanceLog, 'message' => 'Mantenimiento rechazado']);
        }
}
