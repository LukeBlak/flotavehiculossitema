<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFuelLogRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'exists:trips,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'liters' => ['required', 'numeric', 'min:0.01'],
            'cost_per_liter' => ['required', 'numeric', 'min:0'],
            'station_name' => ['required', 'string', 'max:255'],
            'receipt_image' => ['sometimes', 'string'],
            'logged_at' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
