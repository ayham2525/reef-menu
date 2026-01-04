<?php
// app/Http/Requests/Agency/StoreAgencyRequest.php
namespace App\Http\Requests\Agency;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agencies,code,' . $this->route('agency'),
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'license_no' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ];
    }
}
