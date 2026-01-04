<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $item = $this->route('menu_item'); // matches controller signature

        return [
            'menu_category_id'  => ['nullable', 'exists:menu_categories,id'],
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('menu_items', 'slug')->ignore($item->id)->whereNull('deleted_at'),
            ],
            'sku'               => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('menu_items', 'sku')->ignore($item->id)->whereNull('deleted_at'),
            ],
            'description'       => ['nullable', 'string'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'currency'          => ['nullable', 'string', 'size:3'],
            'is_featured'       => ['nullable', 'boolean'],
            'is_available'      => ['nullable', 'boolean'],
            'is_active'         => ['nullable', 'boolean'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0'],
            'calories'          => ['nullable', 'integer', 'min:0'],
            'tags'              => ['nullable', 'string'],
            'allergens'         => ['nullable', 'string'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'mimetypes:image/png,image/x-png', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }
}
