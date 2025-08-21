<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleCompleteTaskTest extends TestCase
{
    use RefreshDatabase;

    // ----- 完了テスト
    public function test_user_can_mark_task_as_completed()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ① ユーザー＆未完了のタスクを作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $user->id,
            'task_category_id' => $category->id,
            'is_completed'     => false,
        ]);

        // ② ログイン
        $this->actingAs($user);

        // ③ 完了切替エンドポイントへ POST
        $response = $this->post(route('tasks.complete', ['task' => $task->id]));

        // ④ 成功(302)
        $response->assertStatus(302);

        // ⑤ DBが完了(true)になっていること
        $this->assertDatabaseHas('tasks', [
            'id'               => $task->id,
            'task_category_id' => $category->id,
            'is_completed'     => true,
        ]);
    }

    // ----- すでに完了状態（is_completed = true）のタスクをもう一度トグルしたとき、未完了（false）に戻るかを確認するテスト
    public function test_user_can_mark_task_as_incomplete_when_already_completed()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ① ユーザー＆完了済みのタスクを作成
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id'          => $user->id,
            'task_category_id' => $category->id,
            'is_completed'     => true,
        ]);

        // ② ログイン
        $this->actingAs($user);

        // ③ 同じエンドポイントに POST（トグル動作）
        $response = $this->post(route('tasks.complete', ['task' => $task->id]));

        // ④ 成功(302)
        $response->assertStatus(302);

        // ⑤ DBが未完(false)になっていること
        $this->assertDatabaseHas('tasks', [
            'id'               => $task->id,
            'task_category_id' => $category->id,
            'is_completed'     => false,
        ]);
    }


    // ----- ログイン中のユーザーが 他人のタスク を勝手にトグルできないことを確認するテスト。
    public function test_user_cannot_toggle_another_users_task()
    {
        // ⓪ task_categoriesに値を入れるため
        $this->seed();
        // TaskCategoryデータを取得
        $category = TaskCategory::first();

        // ① 別ユーザーのタスク
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task  = Task::factory()->create([
            'user_id'          => $owner->id,
            'task_category_id' => $category->id,
            'is_completed'     => false,
        ]);

        // ② ログイン（オーナー以外）
        $this->actingAs($other);

        // ③ 不正アクセスは 404(= assertNotFound)
        $response = $this->post(route('tasks.complete', ['task' => $task->id]));
        $response->assertNotFound();
    }
}
