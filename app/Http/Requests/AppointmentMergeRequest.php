<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentMergeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => 'nullable',
            'specialization' => 'nullable',
            'title' => 'required',
            'description' => 'nullable',
            'job_description' => 'nullable|max:2048',
            'cv' => 'nullable|max:2048',
            'role' => 'required',
            'appointment_date' => 'required',
        ];
    }
}
