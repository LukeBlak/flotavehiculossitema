<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('gerente');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'unique:vehicle_types,code,' . $this->route('vehicle_type')->id],
            'maintenance_interval_km' => ['sometimes', 'integer', 'min:1000'],
            'tank_capacity' => ['sometimes', 'numeric', 'min:10'],
            'payload_capacity' => ['sometimes', 'numeric', 'min:100'],
        ];
    }
}
