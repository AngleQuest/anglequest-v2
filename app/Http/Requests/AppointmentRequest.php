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
            'specialization' => 'required|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
           // 'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpeg,jpg,png|max:2048',
            'prefmode' => 'nullable|string|max:255',
            'priority' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'deadline' => 'nullable|string|max:255',
        ];
    }
}
