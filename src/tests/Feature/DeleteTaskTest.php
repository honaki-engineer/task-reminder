<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    // ----- 自分のタスクを削除できる
    public function test_user_can_delete_own_task()
    {
        // ⓪ 前提：カテゴリ投入
        $this->seed();
        $category = TaskCategory::first();

        // ① ユーザー & タスク作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $user->id,
            'task_category_id' => $category->id,
        ]);

        // ② 認証
        $this->actingAs($user);

        // ③ 削除実行
        $response = $this->delete(route('tasks.destroy', ['task' => $task->id]));

        // ④ レスポンス(今回は削除後にリダイレクトOKか否か)
        $response->assertStatus(302);

        // ⑤ DBから消えていること（物理削除）
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    // ----- 他人のタスクは削除できない
    public function test_user_cannot_delete_others_task()
    {
        // ⓪ 前提：カテゴリ投入
        $this->seed();
        $category = TaskCategory::first();

        // ① ユーザー &.  別ユーザー & タスク作成
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $owner->id,
            'task_category_id' => $category->id,
        ]);

        // ② 認証
        $this->actingAs($other);

        // ③ 削除実行
        $response = $this->delete(route('tasks.destroy', ['task' => $task->id]));

        // ④ 不正アクセスは 404(= assertNotFound)
        $response->assertNotFound();

        // ⑤ まだDBに残っている
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
