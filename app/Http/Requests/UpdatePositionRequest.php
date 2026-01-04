<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('position');
        if (is_object($id)) $id = $id->getKey();

        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:160', Rule::unique('positions', 'slug')->ignore($id)],
            'code'        => ['nullable', 'string', 'max:50', Rule::unique('positions', 'code')->ignore($id)],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
            'sort_order'  => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->slug ?: null,
            'code' => $this->code ?: null,
        ]);
    }
}
