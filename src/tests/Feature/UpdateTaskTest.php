<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_task()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ①ユーザー & タスクを作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'task_category_id' => $category->id,
            'title'   => '編集前タイトル',
        ]);

        // ②ログイン状態にする
        $this->actingAs($user);

        // ③PUT/PATCH送信で編集
        $response = $this->patch("/tasks/{$task->id}", [
            'title'         => '編集後タイトル',
            'description'   => '編集後の詳細',
            'task_category' => 3, 
            'start_date'    => '2025-01-01',
            'start_time'    => '09:00',
            'end_date'      => '2025-01-02',
            'end_time'      => '18:00',
        ]);

        // ④リダイレクトしているか
        $response->assertStatus(302);

        // ⑤DBに編集内容が反映されているか
        $this->assertDatabaseHas('tasks', [
            'id'    => $task->id,
            'task_category_id' => 3,
            'title' => '編集後タイトル',
        ]);
    }
}
