<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        
        // ① テスト用ユーザーを作成してログイン扱いにする
        $user = User::factory()->create();
        $this->actingAs($user);

        // ② POST送信 → タスクを登録
        $response = $this->post('/tasks', [
            'title'         => 'テストタスク',
            'description'   => '詳細',
            'task_category' => 1,
            'start_date'    => '2024-08-23',
            'start_time'    => '10:00',
            'end_date'      => '2024-08-24',
            'end_time'      => '18:00',
            'action'        => 'store_and_index', // redirect先でstoreは必要なため追記
        ]);

        // ③ リダイレクトしている（=成功している）ことを確認
        $response->assertStatus(302);

        // ④ DBにタスクが保存されているか確認
        $this->assertDatabaseHas('tasks', [
            'title'   => 'テストタスク',
            'user_id' => $user->id,
        ]);
    }
}
