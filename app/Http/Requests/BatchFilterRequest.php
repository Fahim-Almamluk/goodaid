<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchFilterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,active',
            'per_page' => 'nullable|integer|in:25,50,100,200',
        ];
    }

    /**
     * Get validated and sanitized filters.
     */
    public function getFilters(): array
    {
        $validated = $this->validated();
        
        // Remove empty values
        return array_filter($validated, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
