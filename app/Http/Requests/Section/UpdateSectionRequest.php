<?php

namespace App\Http\Requests\Section;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: add policies later
    }

    public function rules(): array
    {
        // Handle both route-model binding (object) and plain ID
        $sectionParam = $this->route('section');
        $id = is_object($sectionParam) ? $sectionParam->getKey() : $sectionParam;

        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:160', Rule::unique('sections', 'slug')->ignore($id)],
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('sections', 'code')->ignore($id)],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'exists:sections,id', 'not_in:' . $id], // prevent self-parenting
            'is_active'   => ['sometimes', 'boolean'],
            'sort_order'  => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /**
     * Normalize input before validation passes to the controller.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->slug ?: null,
            'code' => $this->code ?: null,
        ]);
    }
}
