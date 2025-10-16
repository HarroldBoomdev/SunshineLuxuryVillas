<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or your permission logic
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            // Years
            'year_construction' => ['nullable', 'integer', "between:1900,{$currentYear}"],
            'year_renovation'   => ['nullable', 'integer', "between:1900,{$currentYear}", 'gte:year_construction'],

            // Core fields
            'title'         => ['required', 'string', 'max:255'],
            'price'         => ['nullable', 'numeric'],
            'property_type' => ['required', 'string'],

            // NEW: Property Status ('' = None, 'Active' = publish)
            'property_status' => ['nullable', 'in:Active,'],

            // Optional sync tracking (usually set by job)
            'published_at'  => ['nullable', 'date'],
            'external_slug' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'year_renovation.gte' => 'Renovation year cannot be earlier than the construction year.',
            'property_status.in'  => 'Property Status must be either "Active" or "None".',
        ];
    }
}
