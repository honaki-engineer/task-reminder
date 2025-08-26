<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SlackNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlackDisconnectTest extends TestCase
{
    use RefreshDatabase;

    // ----- disconnect
    public function test_disconnect_nullifies_only_authenticated_users_record()
    {
        // ユーザーcreate
        $owner = User::factory()->create();
        $other = User::factory()->create();

        // それぞれにSlackNotificationに別レコードを用意
        SlackNotification::forceCreate([
            'user_id' => $owner->id,
            'slack_user_id' => 'U_OWNER',
            'slack_team_id' => 'T1',
            'bot_access_token' => 'xoxb-owner',
            'is_enabled' => true,
        ]);
        SlackNotification::forceCreate([
            'user_id' => $other->id,
            'slack_user_id' => 'U_OTHER', 'slack_team_id' => 'T1',
            'bot_access_token' => 'xoxb-other', 'is_enabled' => true,
        ]);

        // Slack連携解除
        $this->actingAs($owner)
             ->post(route('slack.disconnect'))
             ->assertStatus(302)
             ->assertSessionHas('success');

        // 結果
        $this->assertDatabaseHas('slack_notifications', [
            'user_id' => $owner->id,
            'slack_user_id' => null,
            'slack_team_id' => null,
            'bot_access_token' => null,
            'is_enabled' => false,
        ]);
        $this->assertDatabaseHas('slack_notifications', [ // other は無傷
            'user_id' => $other->id,
            'slack_user_id' => 'U_OTHER',
            'bot_access_token' => 'xoxb-other',
            'is_enabled' => true,
        ]);
    }
}
