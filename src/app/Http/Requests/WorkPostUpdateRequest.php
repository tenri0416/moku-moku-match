<?php

namespace App\Http\Requests;

use App\Models\WorkPost;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkPostUpdateRequest extends FormRequest
{
    /**
     * 募集更新リクエストを許可するか判定する。
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * 募集更新時のバリデーションルール。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:5000'],
            'purpose' => ['nullable', 'string', 'max:50'],
            'meeting_url' => ['nullable', 'string', 'max:255'],
            'location_type' => [
                'nullable',
                Rule::in([
                    WorkPost::LOCATION_ONLINE,
                    WorkPost::LOCATION_OFFLINE,
                    WorkPost::LOCATION_BOTH,
                ]),
            ],
            'meeting_tool' => ['nullable', 'string', 'max:100'],
            'prefecture_id' => ['nullable', 'integer', 'exists:prefectures,id'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'time_zone' => [
                'nullable',
                Rule::in([
                    WorkPost::TIME_ZONE_MORNING,
                    WorkPost::TIME_ZONE_DAYTIME,
                    WorkPost::TIME_ZONE_NIGHT,
                ]),
            ],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
