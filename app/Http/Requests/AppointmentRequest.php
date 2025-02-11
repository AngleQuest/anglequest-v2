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
            'specialization' => 'required|max:255',
            'title' => 'required',
            'description' => 'required',
            // 'attachment' => 'required,doc,docx,jpeg,jpg,png|max:2048',
            'prefmode' => 'required',
            'priority' => 'required',
            'name' => 'required',
            'role' => 'required',
            'appointment_date' => 'required',
        ];
    }
}
