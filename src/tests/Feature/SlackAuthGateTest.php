<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlackAuthGateTest extends TestCase
{

    use RefreshDatabase;

    // ----- 未ログイン時loginへ遷移(toggle/disconnect/callback/authorize)
    public function test_guest_cannot_toggle()
    {
        $this->post(route('slack.toggle'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }
    public function test_guest_cannot_disconnect()
    {
        $this->post(route('slack.disconnect'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }
    public function test_guest_cannot_handle_callback()
    {
        $this->get(route('slack.callback', ['code' => 'DUMMY']))
            ->assertStatus(302)
             ->assertRedirect(route('login'));
    }
    public function test_guest_cannot_redirect_to_slack()
    {
        $this->get(route('slack.redirect'))
            ->assertStatus(302)
             ->assertRedirect(route('login'));
    }
    public function test_guest_cannot_slack_index()
    {
        $this->get(route('slacks.index'))
            ->assertStatus(302)
             ->assertRedirect(route('login'));
    }
}
