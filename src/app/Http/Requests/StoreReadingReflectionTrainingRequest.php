<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReadingReflectionTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'read_on' => ['nullable', 'date', 'before_or_equal:today'],
            'book_title' => ['nullable', 'string', 'max:100'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:240'],
            'mood' => ['nullable', 'string', 'in:good,normal,difficult'],
            'reflection_text' => ['required', 'string', 'min:5', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'read_on' => '読書日',
            'book_title' => '本のタイトル',
            'read_minutes' => '読書時間',
            'mood' => '読書後の感覚',
            'reflection_text' => '自分なりの解釈・感想',
        ];
    }

    public function messages(): array
    {
        return [
            'reflection_text.required' => '読書の振り返りを入力してください。',
            'reflection_text.min' => '読書の振り返りは5文字以上で入力してください。',
            'reflection_text.max' => '読書の振り返りは5000文字以内で入力してください。',
            'read_on.before_or_equal' => '未来の日付は選択できません。',
        ];
    }
}
