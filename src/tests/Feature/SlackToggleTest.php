<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SlackNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlackToggleTest extends TestCase
{
    use RefreshDatabase;

    // ----- 通知ON/OFF
    public function test_toggle_affects_only_authenticated_users_record()
    {
        // ユーザーcreate
        $owner = User::factory()->create();
        $other = User::factory()->create();

        // それぞれに別レコードを用意
        SlackNotification::create([
            'user_id' => $owner->id,
            'slack_user_id' => 'U_OWNER',
            'slack_team_id' => 'T1',
            'bot_access_token' => 'xoxb-owner', 
            'is_enabled' => false,
        ]);
        SlackNotification::create([
            'user_id' => $other->id,
            'slack_user_id' => 'U_OTHER',
            'slack_team_id' => 'T1',
            'bot_access_token' => 'xoxb-other',
            'is_enabled' => true,
        ]);

        // owner がトグル → owner のみ反転し、other は不変
        $this->actingAs($owner) // 認証
             ->post(route('slack.toggle'))
             ->assertStatus(302)
             ->assertSessionHas('success');

        // 結果
        $this->assertDatabaseHas('slack_notifications', [
            'user_id' => $owner->id, 'is_enabled' => true,
        ]);
        $this->assertDatabaseHas('slack_notifications', [
            'user_id' => $other->id, 'is_enabled' => true, // 変更されない
        ]);
    }
}
