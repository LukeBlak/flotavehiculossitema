<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['gerente', 'supervisor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_type_id' => ['sometimes', 'exists:vehicle_types,id'],
            'plate_number' => ['sometimes', 'string', 'unique:vehicles,plate_number,' . $this->route('vehicle')->id],
            'brand' => ['sometimes', 'string', 'max:255'],
            'model' => ['sometimes', 'string', 'max:255'],
            'year' => ['sometimes', 'integer', 'between:1900,' . date('Y')],
            'status' => ['sometimes', 'in:active,inactive,maintenance'],
            'current_odometer' => ['sometimes', 'integer', 'min:0'],
            'assigned_driver_id' => ['sometimes', 'nullable', 'exists:users,id'],
        ];
    }
}
