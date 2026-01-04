<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'broker_id'   => ['nullable', 'uuid', 'exists:brokers,id'],

            'agency_name' => ['required', 'string', 'max:255'],
            'notes'       => ['nullable', 'string', 'max:2000'],

            'items'              => ['required', 'array', 'min:1'],
            'items.*.item_name'  => ['required', 'string', 'max:255'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],

            // optional - for inventory deduction
            'items.*.menu_item_id' => ['nullable'],
        ];
    }
}
