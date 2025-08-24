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

        // ----- Slack連携情報
        $slackNotification = $user->slackNotification;

        return view('slacks.index', compact('slackNotification'));
    }


    // Slack認証画面へリダイレクトする：Slack連携をスタートするための“導入ゲート”
    public function redirectToSlack()
    {
        // -----  Slack認証画面へリダイレクト
        $url = "https://slack.com/oauth/v2/authorize";
        $params = [
            'client_id'     => env('SLACK_CLIENT_ID'),
            'scope'         => 'chat:write,channels:read,users:read',
            'redirect_uri'  => env('SLACK_REDIRECT_URI'),
        ];

        return redirect($url . '?' . http_build_query($params));
    }


    // 認証後にSlackから飛んでくる場所
    public function handleCallback(Request $request)
    {
        // ----- 未ログインの場合処理中断
        if(!auth()->check()) {
            abort(403);
        }

        // ----- SlackのOAuth認可でSlack側から返ってきた認可コード
        $code = $request->code;

        // ----- Slackから返された認可コードを使って、アクセストークンを取得するリクエストをSlackに送信
        $response = Http::asForm()->post('https://slack.com/api/oauth.v2.access',[
            'client_id'     => env('SLACK_CLIENT_ID'),
            'client_secret' => env('SLACK_CLIENT_SECRET'),
            'code'          => $code,
            'redirect_uri'  => env('SLACK_REDIRECT_URI'),
        ])->json();

        // ----- ユーザーごとのアクセストークンを保存
        $token       = $response['access_token'];    // ユーザー用
        $team_id     = $response['team']['id'];
        $slack_user  = $response['authed_user']['id'];

        // ----- 更新
        SlackNotification::updateOrCreate(
            ['user_id' => auth()->id()],    // ログイン中ユーザー
            [
                'slack_user_id'    => $slack_user,
                'slack_team_id'    => $team_id,
                'bot_access_token' => $token,
                'is_enabled'       => true
            ]
        );

        return to_route('slacks.index')->with('success','Slack連携が完了しました！');
    }


    // Slack連携解除
    public function disconnect()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- Slack連携情報
        $slackNotification = $user->slackNotification;

        // ----- Slack連携解除
        if($slackNotification) {
            // ①物理削除したい場合
            // $slackNotification->delete();

            // ②残したまま無効化したい場合（推奨）
            $slackNotification->update([
                'slack_user_id'    => null,
                'slack_team_id'    => null,
                'bot_access_token' => null,
                'is_enabled'       => false,
            ]);
        }

        return to_route('slacks.index')->with('success', 'Slack連携を解除しました。');
    }


    // 通知toggle
    public function toggle()
    {
        // ----- ユーザー情報
        $user = TaskService::getUser();

        // ----- Slack通知情報
        $slackNotification = $user->slackNotification;

        // ----- 通知toggle
        if(!$slackNotification) {
           return to_route('slacks.index')->with('error', 'Slack連携がまだ完了していません。');
        }

        $slackNotification->update([
            'is_enabled' => ! $slackNotification->is_enabled
        ]);

        // ----- フラッシュメッセージ作成
        $is_enabled = $slackNotification->is_enabled;
        $flashMessage = $is_enabled ? 'ON' : 'OFF';

        return to_route('slacks.index')->with('success','毎朝の通知設定を' . $flashMessage . 'に変更しました。');
    }
}
