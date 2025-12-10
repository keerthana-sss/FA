<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'payer_id' => 'required|integer|exists:users,id',
            'payee_id' => 'nullable|integer|exists:users,id',
            'payee_ids' => 'nullable|array',
            'payee_ids.*' => 'integer|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string',
            'description' => 'nullable|string',
            'split_type' => 'required|in:single,equal'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if ($this->split_type === 'single' && empty($this->payee_id)) {
                $validator->errors()->add('payee_id', 'payee_id is required for single type.');
            }

            if ($this->split_type === 'equal' && empty($this->payee_ids)) {
                $validator->errors()->add('payee_ids', 'payee_ids array is required for equal split.');
            }
        });
    }
}
