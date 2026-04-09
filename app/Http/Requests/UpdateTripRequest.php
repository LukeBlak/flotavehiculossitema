<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $trip = $this->route('trip');
        return $this->user()->hasRole('supervisor') || 
               ($this->user()->hasRole('motorista') && $trip->driver_id === $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'end_odometer' => ['sometimes', 'integer', 'min:' . ($this->route('trip')->start_odometer ?? 0)],
            'end_time' => ['sometimes', 'date_format:Y-m-d H:i:s', 'after_or_equal:' . ($this->route('trip')->start_time ?? now())],
            'status' => ['sometimes', 'in:scheduled,in_progress,completed,cancelled'],
            'route_description' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
