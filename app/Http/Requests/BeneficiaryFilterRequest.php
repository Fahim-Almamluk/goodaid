<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BeneficiaryFilterRequest extends FormRequest
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
            'beneficiary_id' => 'nullable|integer|exists:beneficiaries,id',
            'residence_status' => 'nullable|string|in:resident,displaced',
            'relationship' => 'nullable|string|in:زوج/ة,أرمل/ة',
            'status' => 'nullable|string|in:active,inactive',
            'has_pregnant' => 'nullable|boolean',
            'has_nursing' => 'nullable|boolean',
            'has_children' => 'nullable|boolean',
            'filter_members' => 'nullable|boolean',
            'age_min' => 'nullable|integer|min:0|max:120',
            'age_max' => 'nullable|integer|min:0|max:120',
            'members_min' => 'nullable|integer|min:1',
            'members_max' => 'nullable|integer|min:1',
            'batch_id' => 'nullable|integer|exists:batches,id',
            'per_page' => 'nullable|integer|in:25,50,100,200',
        ];
    }

    /**
     * Get validated and sanitized filters.
     */
    public function getFilters(): array
    {
        $validated = $this->validated();
        
        // Convert checkbox values to boolean
        $booleanFields = ['has_pregnant', 'has_nursing', 'has_children', 'filter_members'];
        foreach ($booleanFields as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field];
        }
        
        // Remove empty values but keep false booleans
        return array_filter($validated, function ($value, $key) use ($booleanFields) {
            if (in_array($key, $booleanFields)) {
                return $value === true;
            }
            return $value !== null && $value !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }
}
