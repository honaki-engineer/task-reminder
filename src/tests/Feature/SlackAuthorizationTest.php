<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SlackNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlackAuthorizationTest extends TestCase
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

    // ----- SlackNotification 未作成時の toggle
    public function test_toggle_without_link_shows_error_flash()
    {
        // SlackNotification 未作成
        $user = User::factory()->create();

        // toggle
        $this->actingAs($user)
             ->post(route('slack.toggle'))
             ->assertStatus(302)
             ->assertSessionHas('error', 'Slack連携がまだ完了していません。');

        // レコード件数確認
        $this->assertDatabaseCount('slack_notifications', 0);
    }
}
