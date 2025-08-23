<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskValidationUpdateTest extends TestCase
{
    use RefreshDatabase;

    // ----- 無効入力 → 302でeditへ戻る／errors保持／既存レコード不変（フロー＋ルール）
    public function test_update_shows_validation_errors_on_invalid_input()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ① ユーザー & タスクを作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $user->id,
            'task_category_id' => $category->id,
            'title'            => '元タイトル',
        ]);

        // ② 認証
        $this->actingAs($user);

        // ③ 不正データを準備
        $payload = [
            'title'         => '', // required NG
            'description'   => null,
            'task_category' => 0, // exists NG
            'start_date'    => '2025-08-23',
            'start_time'    => '10:00',
            'end_date'      => '2025-08-23',
            'end_time'      => '09:00', // 相関NG
        ];

        // ④ PUT/PATCH送信で編集
        $res = $this->from(route('tasks.edit', $task))
                    ->put(route('tasks.update', $task), $payload);

        // ⑤ リダイレクトとエラー確認
        $res->assertStatus(302)
            ->assertRedirect(route('tasks.edit', $task))
            ->assertSessionHasErrors(['title', 'task_category', 'end_at']);

        // ⑥ エラーメッセージ文言を代表確認（カスタムメッセージ）
        $this->followRedirects($res)
             ->assertSee('タスクは必ず指定してください。')
             ->assertSee('選択されたフォーカスマトリックスは正しくありません。')
             ->assertSee('締切日時は開始日時以降にしてください。');

        // ⑦ 既存データは変更されていない（副作用なし）
        $this->assertDatabaseHas('tasks', [
            'id'    => $task->id,
            'title' => '元タイトル',
        ]);
    }

    // ----- フォーマットエラー（H:iでない）
    public function test_update_shows_errors_when_time_format_is_invalid()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ① ユーザー & タスクを作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $user->id,
            'task_category_id' => $category->id,
            'title'            => 'OK',
            'start_at'         => now(),
            'end_at'           => now()->addHour(),
        ]);

        // ② 認証
        $this->actingAs($user);

        // ③ 不正データを準備
        $payload = [
            'title'         => '時刻形式テスト',
            'description'   => null,
            'task_category' => 1,
            'start_date'    => '2025-08-23',
            'start_time'    => '25:61', // NG
            'end_date'      => '2025-08-23',
            'end_time'      => 'aa:bb', // NG
        ];

        // ④ PUT/PATCH送信で編集
        $res = $this->from(route('tasks.edit', $task))
                    ->put(route('tasks.update', $task), $payload);

        // ⑤ リダイレクトとエラー確認
        $res->assertStatus(302)
            ->assertRedirect(route('tasks.edit', $task))
            ->assertSessionHasErrors(['start_time', 'end_time']);

        // ⑥ エラーメッセージ文言を代表確認（カスタムメッセージ）
        $this->followRedirects($res)
             ->assertSee('開始時間はH:i形式で指定してください。')
             ->assertSee('締切時間はH:i形式で指定してください。');

        // ⑦ 既存データは変更されていない（副作用なし）
        $this->assertDatabaseHas('tasks', [
            'id'    => $task->id,
            'title' => 'OK',
        ]);
    }
}
