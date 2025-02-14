<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
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
            'category' => 'required',
            'specialization' => 'required',
            'title' => 'required',
            'description' => 'nullable',
            'job_description' => 'nullable|mimetypes:application/pdf',
            'cv' => 'nullable|mimetypes:application/pdf',
            'role' => 'required',
            'appointment_date' => 'required',
        ];
    }
}
