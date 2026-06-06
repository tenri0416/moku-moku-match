<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;

        return [
            'prefecture_id' => ['nullable', 'integer', 'exists:prefectures,id'],
            'title' => ['required', 'string', 'max:150'],

            'slug' => [
                'required',
                'string',
                'max:150',
                'alpha_dash',
                Rule::unique('articles', 'slug')->ignore($articleId),
            ],

            'short_slug' => [
                'nullable',
                'string',
                'max:150',
                'alpha_dash',
                Rule::unique('articles', 'short_slug')->ignore($articleId),
            ],

            'seo_title' => ['nullable', 'string', 'max:150'],
            'seo_description' => ['nullable', 'string', 'max:255'],
            'h1_title' => ['nullable', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_css' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'thumbnail' => ['nullable', 'image', 'max:5120'],

            'author_user_id' => ['nullable', 'exists:users,id'],
            'reading_minutes' => ['required', 'integer', 'min:1', 'max:120'],

            'point_text' => ['nullable', 'string', 'max:2000'],
            'toc_text' => ['nullable', 'string', 'max:2000'],

            'status' => [
                'required',
                'integer',
                Rule::in([
                    Article::STATUS_DRAFT,
                    Article::STATUS_PUBLIC,
                    Article::STATUS_PRIVATE,
                ]),
            ],

            'published_at' => ['nullable', 'date'],
            'article_category_id' => ['nullable', 'exists:article_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:article_tags,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'prefecture_id' => '対象都道府県',
            'title' => '記事タイトル',
            'slug' => 'URL用スラッグ',
            'short_slug' => '短縮URL用スラッグ',
            'seo_title' => 'SEOタイトル',
            'seo_description' => 'SEOディスクリプション',
            'h1_title' => 'H1見出し',
            'excerpt' => '記事概要',
            'body_html' => '本文',
            'thumbnail' => 'サムネイル画像',
            'status' => '公開状態',
            'published_at' => '公開日時',
        ];
    }
}
