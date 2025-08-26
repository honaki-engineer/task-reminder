<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlackRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirectToSlack_redirects_with_correct_query_params()
    {
        // 設定値を固定
        config([
            'services.slack.client_id'    => 'test_client_id',
            'services.slack.scope'        => 'chat:write channels:read users:read',
            'services.slack.redirect_uri' => 'https://example.com/slack/callback',
        ]);

        // ルートにアクセス
        $response = $this->get(route('slack.redirect'))->assertRedirect();
        $location = $response->headers->get('Location');

        // Slack認証URLにリダイレクトしていること
        $this->assertStringStartsWith('https://slack.com/oauth/v2/authorize?', $location);

        // クエリをパースして検証
        parse_str(parse_url($location, PHP_URL_QUERY), $q);
        $scope = str_replace('+', ' ', $q['scope'] ?? '');

        // Slack 認証画面にリダイレクトするときのクエリパラメータが設定どおりになっているか
        $this->assertSame('test_client_id', $q['client_id'] ?? null);
        $this->assertSame('chat:write channels:read users:read', $scope);
        $this->assertSame('https://example.com/slack/callback', $q['redirect_uri'] ?? null);
    }
}
