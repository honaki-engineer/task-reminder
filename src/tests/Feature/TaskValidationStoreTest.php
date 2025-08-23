<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskValidationStoreTest extends TestCase
{
    use RefreshDatabase;

    // ----- 無効入力 → 302でcreateへ戻る／errors保持／DB未作成（フロー＋ルール）
    public function test_store_shows_validation_errors_on_invalid_input()
    {
        // ⓪ 前提：カテゴリ投入
        $this->seed(); // task_categories投入

        // ① ユーザー & タスク作成
        $user = User::factory()->create();

        // ② 認証
        $this->actingAs($user);

        // ③ 不正データを準備
        $payload = [
            'title'         => '', // required NG
            'description'   => str_repeat('a', 2001), // max:2000 NG
            'task_category' => 9999, // exists NG
            'start_date'    => '2025-08-23',
            'start_time'    => '1:00',
            'end_date'      => '2025-08-22', // startより前 → 相関NG
            'end_time'      => '10:00',
            'action'        => 'store_and_index',
        ];

        // ④ POSTリクエスト実行
        $res = $this->from(route('tasks.create'))
                    ->post(route('tasks.store'), $payload);

        // ⑤ リダイレクトとエラー確認
        $res->assertStatus(302)
            ->assertRedirect(route('tasks.create'))
            ->assertSessionHasErrors(['title', 'description', 'task_category', 'end_at']);

        // ⑥ エラーメッセージ文言を代表確認（カスタムメッセージ）
        $this->followRedirects($res)
             ->assertSee('タスクは必ず指定してください。')
             ->assertSee('詳細は2000文字以下で指定してください。')
             ->assertSee('選択されたフォーカスマトリックスは正しくありません。')
             ->assertSee('締切日時は開始日時以降にしてください。');

        // ⑦ DB未作成確認
        $this->assertDatabaseCount('tasks', 0); // 未作成
    }

    // ----- フォーマットエラー（時刻がH:iでない）
    public function test_store_shows_errors_when_time_format_is_invalid()
    {
        // ⓪ 前提：カテゴリ投入
        $this->seed(); // task_categories投入

        // ① ユーザー & タスク作成
        $user = User::factory()->create();

        // ② 認証
        $this->actingAs($user);

        // ③ 不正データを準備
        $payload = [
            'title'         => '時刻形式テスト',
            'task_category' => 1,
            'start_date'    => '2025-08-23',
            'start_time'    => '25:61', // NG
            'end_date'      => '2025-08-23',
            'end_time'      => 'aa:bb', // NG
            'action'        => 'store_and_index',
        ];

        // ④ POSTリクエスト実行
        $res = $this->from(route('tasks.create'))
                    ->post(route('tasks.store'), $payload);

        // ⑤ リダイレクトとエラー確認
        $res->assertStatus(302)
            ->assertRedirect(route('tasks.create'))
            ->assertSessionHasErrors(['start_time', 'end_time']);

        // ⑥ エラーメッセージ文言を代表確認（カスタムメッセージ）
        $this->followRedirects($res)
             ->assertSee('開始時間はH:i形式で指定してください。')
             ->assertSee('締切時間はH:i形式で指定してください。');

        // ⑦ DB未作成確認
        $this->assertDatabaseCount('tasks', 0); // 未作成
    }
}
