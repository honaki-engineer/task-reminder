<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationOtherUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_user_cannot_view_edit_update_delete_or_complete_foreign_task()
    {
        // seeder
        $this->seed();
        $category = TaskCategory::first();

        // create
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task  = Task::factory()->create([
            'user_id' => $owner->id,
            'task_category_id' => $category->id,
            'title' => 'owner task',
            'start_at' => now(),
            'end_at' => now()->addHour(),
            'is_completed' => false,
        ]);

        // 認証
        $this->actingAs($other);

        // show / edit は 404
        $this->get(route('tasks.show', $task))->assertNotFound();
        $this->get(route('tasks.edit', $task))->assertNotFound();

        // update も 404（TaskRequestのキーに合わせる）
        $this->put(route('tasks.update', $task), [
            'title' => 'try update',
            'description' => null,
            'task_category' => $category->id,
            'start_date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_date'   => now()->toDateString(),
            'end_time'   => '11:00',
        ])->assertNotFound();

        // destroy / complete も 404
        $this->delete(route('tasks.destroy', $task))->assertNotFound();
        $this->post(route('tasks.complete', $task))->assertNotFound();

        // レコードは残存
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'user_id' => $owner->id, 'title' => 'owner task']);
    }
}
