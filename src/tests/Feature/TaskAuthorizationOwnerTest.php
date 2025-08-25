<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationOwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_update_complete_and_delete_own_task()
    {
        // seeder
        $this->seed();
        $category = TaskCategory::first();

        // create
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'task_category_id' => $category->id,
            'title' => '元タイトル',
            'start_at' => now(),
            'end_at' => now()->addHour(),
            'is_completed' => false,
        ]);

        // 認証
        $this->actingAs($user);

        // view
        $this->get(route('tasks.show', $task))->assertOk();

        // update（リダイレクトとDB反映）
        $res = $this->from(route('tasks.edit', $task))
                    ->put(route('tasks.update', $task), [
                        'title' => '更新タイトル',
                        'description' => null,
                        'task_category' => $category->id,
                        'start_date' => now()->toDateString(),
                        'start_time' => '10:00',
                        'end_date'   => now()->toDateString(),
                        'end_time'   => '11:00',
                    ]);
        $res->assertStatus(302);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => '更新タイトル']);

        // complete toggle（false → true）
        $this->post(route('tasks.complete', $task))->assertStatus(302);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);

        // delete
        $del = $this->delete(route('tasks.destroy', $task));
        $del->assertStatus(302);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
