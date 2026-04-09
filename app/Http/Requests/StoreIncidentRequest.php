<?php

namespace App\Http\Requests;

use App\Models\Incident;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('motorista');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'exists:trips,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'description' => ['required', 'string', 'max:2000'],
            'reported_at' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $tripId = $this->input('trip_id');
            $vehicleId = $this->input('vehicle_id');
            $reportedAt = $this->input('reported_at');

            if (! $tripId || ! $vehicleId || ! $reportedAt) {
                return;
            }

            $tripTimeConflict = Incident::query()
                ->where('trip_id', $tripId)
                ->where('reported_at', $reportedAt)
                ->whereNull('deleted_at')
                ->exists();

            if ($tripTimeConflict) {
                $validator->errors()->add('reported_at', 'Ya existe un incidente para ese viaje en la misma hora.');
            }

            $vehicleTimeConflict = Incident::query()
                ->where('vehicle_id', $vehicleId)
                ->where('reported_at', $reportedAt)
                ->whereNull('deleted_at')
                ->exists();

            if ($vehicleTimeConflict) {
                $validator->errors()->add('vehicle_id', 'Ese vehículo ya tiene un incidente registrado para esa misma hora.');
            }
        });
    }
}
