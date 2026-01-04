<?php

namespace App\Http\Requests\Agency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Works whether route model binding gives you an Agency model or a plain id/uuid
        $routeValue = $this->route('agency');
        $agencyId   = is_object($routeValue) ? $routeValue->getKey() : $routeValue;

        return [
            'name'        => ['required', 'string', 'max:255'],
            'code'        => [
                'required',
                'string',
                'max:50',
                Rule::unique('agencies', 'code')->ignore($agencyId), // <- key fix
            ],
            'email'       => ['nullable', 'email'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'license_no'  => ['nullable', 'string', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
