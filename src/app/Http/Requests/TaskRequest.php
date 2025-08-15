<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    // バリデーションルールを適用する前に、リクエストの入力データを整形
    protected function prepareForValidation(): void
    {
        // フォームで送られた start_date + start_time を結合して start_at を作成(end_atも同様)
        $this->merge([
            'start_at' => "{$this->start_date} {$this->start_time}",
            'end_at'   => "{$this->end_date} {$this->end_time}",
        ]);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:2000'],
            'task_category' => ['required', 'integer', 'exists:task_categories,id'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_date' => ['required', 'date'],
            'end_time' => ['required', 'date_format:H:i'],
            // 相関チェック(開始≦締切)
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'], // start_at と同じか後の日時であること
        ];
    }


    // 特定のバリデーションルールに対するカスタムエラーメッセージ
    public function messages(): array
    {
        return [
            'end_at.after_or_equal' => '締切日時は開始日時以降にしてください。',
        ];
    }

}
