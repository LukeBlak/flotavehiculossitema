<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceLogRequest extends FormRequest
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
            'type' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'cost' => ['required', 'numeric', 'min:0'],
            'service_date' => ['required', 'date_format:Y-m-d'],
            'workshop_name' => ['required', 'string', 'max:255'],
        ];
    }
}
