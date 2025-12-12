<?php

namespace App\Http\Requests\Itinerary;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'day_number'  => 'sometimes|integer|min:1',
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'required|date_format:Y-m-d H:i',
            'end_time'      => 'required|date_format:Y-m-d H:i|after_or_equal:start_time',
            'location' => 'nullable|string|max:255',
        ];
    }
}
