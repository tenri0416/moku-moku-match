<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WorkPostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:50'],
            'job_type' => ['nullable', 'string', 'max:100'],
            'prefecture_id' => ['nullable', 'integer', 'exists:prefectures,id'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'work_style' => ['nullable', 'string', 'max:255'],
        ];
    }
}
