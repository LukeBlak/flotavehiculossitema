<?php

namespace App\Http\Requests;

use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('supervisor');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:users,id'],
            'start_odometer' => ['required', 'integer', 'min:0'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'route_description' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $vehicleId = $this->input('vehicle_id');
            $driverId = $this->input('driver_id');
            $startTime = $this->input('start_time');
            $routeDescription = $this->input('route_description');

            if (! $vehicleId || ! $driverId || ! $startTime) {
                return;
            }

            $duplicateTrip = Trip::query()
                ->where('vehicle_id', $vehicleId)
                ->where('driver_id', $driverId)
                ->where('start_time', $startTime)
                ->whereNull('deleted_at')
                ->first();

            if ($duplicateTrip) {
                $validator->errors()->add('trip', 'Ya existe un viaje idéntico para ese conductor, vehículo y hora.');
            }

            $vehicleConflict = Trip::query()
                ->where('vehicle_id', $vehicleId)
                ->where('start_time', $startTime)
                ->whereIn('status', ['scheduled', 'assigned', 'in_progress', 'pending_validation'])
                ->whereNull('deleted_at')
                ->first();

            if ($vehicleConflict) {
                $validator->errors()->add('vehicle_id', 'Ese vehículo ya tiene un viaje asignado para esa hora.');
            }

            $driverConflict = Trip::query()
                ->where('driver_id', $driverId)
                ->where('start_time', $startTime)
                ->whereIn('status', ['scheduled', 'assigned', 'in_progress', 'pending_validation'])
                ->whereNull('deleted_at')
                ->first();

            if ($driverConflict) {
                $validator->errors()->add('driver_id', 'Ese conductor ya tiene un viaje asignado para esa hora.');
            }

            if ($routeDescription !== null) {
                $sameRoute = Trip::query()
                    ->where('vehicle_id', $vehicleId)
                    ->where('driver_id', $driverId)
                    ->where('route_description', $routeDescription)
                    ->whereNull('deleted_at')
                    ->first();

                if ($sameRoute) {
                    $validator->errors()->add('route_description', 'Ya existe un viaje registrado con la misma ruta para este vehículo y conductor.');
                }
            }
        });
    }
}
