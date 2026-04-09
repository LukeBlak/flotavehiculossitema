<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceLogRequest extends FormRequest
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
            'type' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2000'],
            'cost' => ['sometimes', 'numeric', 'min:0'],
            'service_date' => ['sometimes', 'date_format:Y-m-d'],
            'workshop_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
        ];
    }
}
