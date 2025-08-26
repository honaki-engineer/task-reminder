<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SlackNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SlackCallbackBindTest extends TestCase
{
    use RefreshDatabase;

    // ----- update
    public function test_callback_creates_or_updates_record_for_authenticated_user()
    {
        // ① 設定をテスト用に差し替え
        config()->set('services.slack.client_id', 'CID');
        config()->set('services.slack.client_secret', 'CSECRET');
        config()->set('services.slack.redirect_uri', 'https://example.com/cb');

        // ② Slack API をモックレスポンス
        Http::fake([ 
            'https://slack.com/api/oauth.v2.access' => Http::response([
                'access_token' => 'xoxb-newtoken',
                'team'        => ['id' => 'TZZZZ'],
                'authed_user' => ['id' => 'UZZZZ'],
            ], 200),
        ]);

        // ③ 他ユーザーの既存データ（影響されないことを担保）
        $other = User::factory()->create();
        SlackNotification::create([
            'user_id'         => $other->id,
            'slack_user_id'   => 'U_OTHER',
            'slack_team_id'   => 'T1',
            'bot_access_token'=> 'xoxb-other',
            'is_enabled'      => true,
        ]);

        // ④ 自分が既に連携済みのケース → updateOrCreate の update を明示検証
        $user = User::factory()->create();
        SlackNotification::create([
            'user_id'         => $user->id,
            'slack_user_id'   => 'U_OLD',
            'slack_team_id'   => 'T_OLD',
            'bot_access_token'=> 'xoxb-old',
            'is_enabled'      => false,
        ]);

        // ⑤ 実行（ログインでコールバック）
        $res = $this->actingAs($user)
                    ->get(route('slack.callback', ['code' => 'XYZ']));

        // ⑥ リダイレクト & フラッシュ確認
        $res->assertStatus(302)
            ->assertRedirect(route('slacks.index'))
            ->assertSessionHas('success');

        // ⑦ 自分レコードが新値で更新され、有効化される
        $this->assertDatabaseHas('slack_notifications', [
            'user_id'         => $user->id,
            'slack_user_id'   => 'UZZZZ',
            'slack_team_id'   => 'TZZZZ',
            'bot_access_token'=> 'xoxb-newtoken',
            'is_enabled'      => true,
        ]);

        // ⑧ 他人レコードはそのまま
        $this->assertDatabaseHas('slack_notifications', [
            'user_id'         => $other->id,
            'slack_user_id'   => 'U_OTHER',
            'bot_access_token'=> 'xoxb-other',
        ]);

        // ⑨ Slack API への送信内容を検証
        Http::assertSent(function ($request) {
            return $request->url() === 'https://slack.com/api/oauth.v2.access'
                && $request->method() === 'POST'
                && $request->isForm()
                && $request['client_id']     === config('services.slack.client_id')
                && $request['client_secret'] === config('services.slack.client_secret')
                && $request['redirect_uri']  === config('services.slack.redirect_uri')
                && $request['code']          === 'XYZ';
        });
    }

    // ----- create
    public function test_callback_creates_record_when_user_has_no_slack_notification()
    {
        config()->set('services.slack.client_id', 'CID');
        config()->set('services.slack.client_secret', 'CSECRET');
        config()->set('services.slack.redirect_uri', 'https://example.com/cb');

        Http::fake([
            'https://slack.com/api/oauth.v2.access' => Http::response([
                'access_token' => 'xoxb-createtoken',
                'team'        => ['id' => 'TNEW'],
                'authed_user' => ['id' => 'UNEW'],
            ], 200),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('slack.callback', ['code' => 'ABC']))
            ->assertRedirect(route('slacks.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('slack_notifications', [
            'user_id'       => $user->id,
            'slack_user_id' => 'UNEW',
            'slack_team_id' => 'TNEW',
            'bot_access_token' => 'xoxb-createtoken',
            'is_enabled'    => true,
        ]);
    }

}
