<?php

namespace App\Http\Requests\Broker;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrokerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brokerId = optional($this->route('broker'))->id; // works if route model binding

        return [
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => [
                'nullable',
                'email',
                Rule::unique('brokers', 'email')->ignore($brokerId), // <— key change
            ],
            'phone'     => ['nullable', 'string', 'max:50'],
            'brn'       => ['nullable', 'string', 'max:100'],
            'is_active' => ['required', 'in:0,1'], // enforced to 0 or 1
        ];
    }
}
