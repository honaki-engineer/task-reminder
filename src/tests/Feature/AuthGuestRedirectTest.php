<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthGuestRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_on_tasks_and_slack_routes()
    {
        $this->seed();

        $owner = User::factory()->create();
        $task  = Task::factory()->create([
            'user_id' => $owner->id,
            'task_category_id' => TaskCategory::first()->id,
        ]);

        // Task系（一覧/作成/閲覧/編集/保存/更新/削除/完了/1日表示）
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
        $this->get(route('tasks.create'))->assertRedirect(route('login'));
        $this->get(route('tasks.show', $task))->assertRedirect(route('login'));
        $this->get(route('tasks.edit', $task))->assertRedirect(route('login'));
        $this->post(route('tasks.store'))->assertRedirect(route('login'));
        $this->put(route('tasks.update', $task))->assertRedirect(route('login'));
        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('login'));
        $this->post(route('tasks.complete', $task))->assertRedirect(route('login'));
        $this->get(route('tasks.one_day'))->assertRedirect(route('login'));

        // Slack系（連携画面/認証/コールバック/解除/トグル）
        $this->get(route('slacks.index'))->assertRedirect(route('login'));
        $this->get(route('slack.authorize'))->assertRedirect(route('login'));
        $this->get(route('slack.callback'))->assertRedirect(route('login'));
        $this->post(route('slack.disconnect'))->assertRedirect(route('login'));
        $this->post(route('slack.toggle'))->assertRedirect(route('login'));
    }
}
