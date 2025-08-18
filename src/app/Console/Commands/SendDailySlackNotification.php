<?php

namespace App\Console\Commands;

use App\Models\SlackNotification;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendDailySlackNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-slack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Slackに毎日通知する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
{
    $users = SlackNotification::where('is_enabled', true)->get();

    foreach ($users as $user) {
        $text = now()->locale('ja')->translatedFormat('----- n月j日(D)') . " タスク一覧 -----\n";

        // ①締切切れ
        $expired = Task::where('user_id',$user->user_id)
            ->whereDate('end_at','<', now()->toDateString())->get();
        $text .= "--- ① 期限切れ\n";
        foreach($expired as $t){
            $text .= "・{$t->title}\n";
        }

        // ②当日締切
        $today = Task::where('user_id',$user->user_id)
            ->whereDate('end_at','=', now()->toDateString())->get();
        $text .= "--- ② 当日締切\n";
        foreach($today as $t){
            $text .= "・{$t->title}\n";
        }

        // ③その他（①②以外）
        $otherCount = Task::where('user_id', $user->user_id)
            ->whereDate('end_at', '>', now()->toDateString())    // 明日以降
            ->count();

        $text .= "--- ③ その他タスク\n";
        $text .= "・現在 {$otherCount} 件あります。\n";

        Http::withToken($user->bot_access_token)
            ->post('https://slack.com/api/chat.postMessage',[
                'channel' => $user->slack_user_id,
                'text'    => $text,
            ]);
    }
}

}
