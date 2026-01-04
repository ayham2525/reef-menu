<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee'); // Employee model via route model binding
        $user     = $employee->user;

        return [
            // User fields (email unique except current)
            'user_name'                  => ['required', 'string', 'max:255'],
            'user_email'                 => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'user_password'              => ['nullable', 'string', 'min:8', 'confirmed'],

            // Employee fields
            'position_id'                => ['required', 'exists:positions,id'],
            'section_id'                 => ['nullable', 'exists:sections,id'],
            'employee_code'              => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employee->id)],
            'phone'                      => ['nullable', 'string', 'max:50'],
            'national_id'                => ['nullable', 'string', 'max:100'],
            'gender'                     => ['nullable', 'in:male,female,other'],
            'birth_date'                 => ['nullable', 'date'],
            'hired_at'                   => ['nullable', 'date'],
            'terminated_at'              => ['nullable', 'date', 'after_or_equal:hired_at'],
            'is_active'                  => ['nullable', 'boolean'],
            'notes'                      => ['nullable', 'string'],
        ];
    }
}
