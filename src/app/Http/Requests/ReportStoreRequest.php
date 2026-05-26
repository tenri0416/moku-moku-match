<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
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
            'reported_user_id' => ['nullable', 'exists:users,id'],
            'work_post_id' => ['nullable', 'exists:work_posts,id'],
            'reason' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->reported_user_id && ! $this->work_post_id) {
                $validator->errors()->add('target', '通報対象を指定してください。');
            }
        });
    }
}
