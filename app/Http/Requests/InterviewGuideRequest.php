<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterviewGuideRequest extends FormRequest
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
            'specialization' => 'required',
            'topic' => 'required',
            'available_days' => 'required',
            'available_time' => 'required',
            'description' => 'required',
            'guides' => 'required',
            'location' => 'required',
            'time_zone' => 'required',
        ];
    }
}
