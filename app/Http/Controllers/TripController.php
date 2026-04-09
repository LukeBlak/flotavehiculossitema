<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole(['gerente', 'supervisor'])) {
            $trips = Trip::with('vehicle', 'driver')->get();
        } else {
            $trips = Trip::where('driver_id', $user->id)->with('vehicle', 'driver')->get();
        }

        return response()->json(['data' => $trips]);
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
    public function store(StoreTripRequest $request)
    {
        $trip = Trip::create($request->validated());
        return response()->json(['data' => $trip], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        $this->authorize('view', $trip);
        $trip->load('vehicle', 'driver', 'fuelLogs', 'incidents');
        return response()->json(['data' => $trip]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trip $trip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);
        $trip->update($request->validated());
        return response()->json(['data' => $trip]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        $this->authorize('delete', $trip);
        $trip->delete();
        return response()->json(['message' => 'Viaje eliminado'], 204);
    }

    /**
     * Asignar vehículo/conductor al viaje.
     */
    public function assign(Request $request, Trip $trip)
    {
        $this->authorize('assign', Trip::class);

        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:users,id'],
            'start_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'route_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $startTime = $data['start_time'] ?? $trip->start_time;

        if (! $startTime) {
            throw ValidationException::withMessages([
                'start_time' => ['El viaje debe tener una fecha para poder asignarse.'],
            ]);
        }

        $vehicleConflict = Trip::query()
            ->whereKeyNot($trip->id)
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('start_time', $startTime)
            ->whereIn('status', ['scheduled', 'assigned', 'in_progress', 'pending_validation'])
            ->whereNull('deleted_at')
            ->exists();

        if ($vehicleConflict) {
            throw ValidationException::withMessages([
                'vehicle_id' => ['Ese vehículo ya está ocupado en otro viaje para la misma hora.'],
            ]);
        }

        $driverConflict = Trip::query()
            ->whereKeyNot($trip->id)
            ->where('driver_id', $data['driver_id'])
            ->where('start_time', $startTime)
            ->whereIn('status', ['scheduled', 'assigned', 'in_progress', 'pending_validation'])
            ->whereNull('deleted_at')
            ->exists();

        if ($driverConflict) {
            throw ValidationException::withMessages([
                'driver_id' => ['Ese motorista ya tiene otro viaje a la misma hora.'],
            ]);
        }

        $duplicateTrip = Trip::query()
            ->whereKeyNot($trip->id)
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('driver_id', $data['driver_id'])
            ->where('start_time', $startTime)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplicateTrip) {
            throw ValidationException::withMessages([
                'trip' => ['Ya existe un viaje replicado con ese vehículo, conductor y fecha.'],
            ]);
        }

        $trip->update([
            'vehicle_id' => $data['vehicle_id'],
            'driver_id' => $data['driver_id'],
            'start_time' => $startTime,
            'route_description' => $data['route_description'] ?? $trip->route_description,
            'status' => 'assigned',
        ]);

        return response()->json(['data' => $trip->fresh(), 'message' => 'Viaje asignado']);
    }

    /**
     * Iniciar viaje (motorista asignado).
     */
    public function start(Request $request, Trip $trip)
    {
        $this->authorize('registerKm', $trip);

        if ($trip->status === 'cancelled') {
            throw ValidationException::withMessages([
                'status' => ['No se puede iniciar un viaje cancelado.'],
            ]);
        }

        if (! in_array($trip->status, ['assigned', 'scheduled'], true)) {
            throw ValidationException::withMessages([
                'status' => ['Solo se puede iniciar un viaje en estado assigned o scheduled.'],
            ]);
        }

        $data = $request->validate([
            'start_odometer' => ['required', 'integer', 'min:0'],
            'start_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
        ]);

        if (! $trip->start_time) {
            throw ValidationException::withMessages([
                'start_time' => ['El viaje no tiene una fecha programada por el supervisor.'],
            ]);
        }

        $scheduledStart = Carbon::parse($trip->start_time);
        $actualStart = isset($data['start_time'])
            ? Carbon::parse($data['start_time'])
            : now();

        if (! $actualStart->isSameDay($scheduledStart)) {
            throw ValidationException::withMessages([
                'start_time' => ['El viaje solo puede iniciarse en la fecha programada por el supervisor.'],
            ]);
        }

        $trip->update([
            'start_odometer' => $data['start_odometer'],
            'start_time' => $actualStart->format('Y-m-d H:i:s'),
            'status' => 'in_progress',
        ]);

        return response()->json(['data' => $trip->fresh(), 'message' => 'Viaje iniciado']);
    }

    /**
     * Completar viaje (motorista asignado).
     */
    public function complete(Request $request, Trip $trip)
    {
        $this->authorize('registerKm', $trip);

        $data = $request->validate([
            'end_odometer' => ['required', 'integer', 'min:' . (int) $trip->start_odometer],
            'end_time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'route_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $trip->update([
            'end_odometer' => $data['end_odometer'],
            'end_time' => $data['end_time'] ?? now()->format('Y-m-d H:i:s'),
            'route_description' => $data['route_description'] ?? $trip->route_description,
            'status' => 'pending_validation',
        ]);

        return response()->json(['data' => $trip->fresh(), 'message' => 'Viaje enviado a validación']);
    }

    /**
     * Validar cierre del viaje (supervisor).
     */
    public function validateTrip(Trip $trip)
    {
        $this->authorize('validate', Trip::class);

        $trip->update(['status' => 'completed']);

        return response()->json(['data' => $trip->fresh(), 'message' => 'Viaje validado y completado']);
    }

    /**
     * Cancelar viaje (supervisor/gerente).
     */
    public function cancel(Request $request, Trip $trip)
    {
        $this->authorize('delete', $trip);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $trip->update(['status' => 'cancelled']);

        return response()->json(['data' => $trip->fresh(), 'message' => 'Viaje cancelado']);
    }
}
