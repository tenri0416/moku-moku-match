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
            

        //             'user_id',
        // 'display_name',
        // 'job_type',
        // 'prefecture',
        // 'skills',
        // 'bio',
        // 'purpose',
        // 'work_style',


        ];
    }
}
