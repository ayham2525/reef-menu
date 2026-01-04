<?php
// app/Http/Requests/StoreBrokerRequest.php
namespace App\Http\Requests\Broker;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrokerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'agency_id' => 'nullable|exists:agencies,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:brokers,email,' . $this->route('broker'),
            'phone' => 'nullable|string|max:50',
            'brn'  => 'nullable|string|max:100',
            'is_active' => 'required|in:0,1',
        ];
    }
}
