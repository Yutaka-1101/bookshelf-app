<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => [
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($this->book),
            ],
            'published_date' => 'required|date',
            'genre_ids' => 'required|array|min:1',
            'genre_ids.*' => 'integer|exists:genres,id',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string|url',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'author.required' => '著者は必須です。',
            'author.string' => '著者は文字列で入力してください。',
            'author.max' => '著者は255文字以内で入力してください。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.size' => 'ISBNは13桁で入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',
            'published_date.required' => '出版日は必須です。',
            'published_date.date' => '有効な日付を入力してください。',
            'genre_ids.required' => 'ジャンルを1つ以上選択してください。',
            'genre_ids.array' => 'ジャンルの形式が不正です。',
            'genre_ids.min' => 'ジャンルを1つ以上選択してください。',
            'genre_ids.*.integer' => '不正なジャンルです。',
            'genre_ids.*.exists' => '存在しないジャンルIDが選択されています。',
            'image_url.url' => '画像URLの形式が正しくありません。',
        ];
    }
}
