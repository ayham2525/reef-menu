<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'menu_category_id' => ['nullable', 'exists:menu_categories,id'],
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255'],
            'sku'              => ['nullable', 'string', 'max:100'],
            'price'            => ['nullable', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'max:5'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0'],
            'calories'         => ['nullable', 'integer', 'min:0'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
            'description'      => ['nullable', 'string'],
            'tags'             => ['nullable', 'string'],      // comma separated
            'allergens'        => ['nullable', 'string'],      // comma separated
            'is_featured'      => ['nullable', 'boolean'],
            'is_available'     => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimetypes:image/png,image/x-png,image/jpeg,image/webp', 'max:5120'],
        ];
    }
}
