<?php

namespace App\Http\Controllers;

use App\Models\SlackNotification;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SlackController extends Controller
{
    public function index()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        return view('slacks.index');
    }

    public function redirectToSlack()
    {
        $url = "https://slack.com/oauth/v2/authorize";
        $params = [
            'client_id'     => env('SLACK_CLIENT_ID'),
            'scope'         => 'chat:write,channels:read,users:read',
            'redirect_uri'  => env('SLACK_REDIRECT_URI'),
        ];
        return redirect($url . '?' . http_build_query($params));
    }

    public function handleCallback(Request $request)
    {
        $code = $request->code;

        $response = Http::asForm()->post('https://slack.com/api/oauth.v2.access',[
            'client_id'     => env('SLACK_CLIENT_ID'),
            'client_secret' => env('SLACK_CLIENT_SECRET'),
            'code'          => $code,
            'redirect_uri'  => env('SLACK_REDIRECT_URI'),
        ])->json();

        // ユーザーごとのアクセストークンを保存
        $token       = $response['access_token'];    // ユーザー用
        $team_id     = $response['team']['id'];
        $slack_user  = $response['authed_user']['id'];

        SlackNotification::updateOrCreate(
            ['user_id' => auth()->id()],    // ログイン中ユーザー
            [
                'slack_user_id'    => $slack_user,
                'slack_team_id'    => $team_id,
                'bot_access_token' => $token,
                'is_enabled'       => true
            ]
        );

        return to_route('tasks.one_day')->with('success','Slack連携が完了しました！');
    }
}
