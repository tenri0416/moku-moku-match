<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'max:255'],
            'introduction' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'purpose' => ['nullable', 'string'],
            'work_style' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'prefecture_id' => ['nullable', 'integer', 'exists:prefectures,id'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
