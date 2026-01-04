<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: add policies later
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:160', 'unique:sections,slug'],
            'code'        => ['nullable', 'string', 'max:50', 'unique:sections,code'],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'exists:sections,id'],
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
