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
        // ----- Slack通知設定を有効にしているユーザー全員
        $users = SlackNotification::where('is_enabled', true)->get();


        // ----- タスク一覧
        foreach($users as $user) {
            $text = now()->locale('ja')->translatedFormat('----- n月j日(D)') . " タスク一覧 -----\n";

            // 共通：未完了のタスク
            $baseQuery = Task::where('user_id', $user->user_id)
                            ->where('is_completed', false);

            // ①期限切れ
            $expired = (clone $baseQuery)
                ->whereDate('end_at', '<', now()->toDateString())
                ->get();
            $text .= "【① 期限切れ】\n";
            foreach ($expired as $task) {
                $text .= "・{$task->title}\n";
            }

            // ②本日締切
            $today = (clone $baseQuery)
                ->whereDate('end_at', now()->toDateString())
                ->get();
            $text .= "【② 当日締切】\n";
            foreach ($today as $task) {
                $text .= "・{$task->title}\n";
            }

            // 明日以降のみ
            $futureQuery = (clone $baseQuery)
                ->whereDate('end_at', '>', now()->toDateString());

            // ③重要＆緊急 → slug: important-urgent
            $importantUrgent = (clone $futureQuery)
                ->whereHas('taskCategory', fn($q) =>
                    $q->where('slug', 'important-urgent'))
                ->get();
            $text .= "【③ 重要＆緊急】\n";
            foreach ($importantUrgent as $task) {
                $text .= "・{$task->title}\n";
            }

            // ④重要（slug: important）
            $important = (clone $futureQuery)
                ->whereHas('taskCategory', fn($q) =>
                    $q->where('slug', 'important'))
                ->get();
            $text .= "【④ 重要】\n";
            foreach ($important as $task) {
                $text .= "・{$task->title}\n";
            }

            // ⑤緊急（slug: urgent）
            $urgent = (clone $futureQuery)
                ->whereHas('taskCategory', fn($q) =>
                    $q->where('slug', 'urgent'))
                ->get();
            $text .= "【⑤ 緊急】\n";
            foreach ($urgent as $task) {
                $text .= "・{$task->title}\n";
            }

            // ⑥その他（slug: other or NULL）
            $others = (clone $futureQuery)
                ->where(function ($q) {
                    $q->whereNull('task_category_id') // カテゴリー無
                        ->orWhereHas('taskCategory', function ($qq) {
                            $qq->where('slug', 'other'); // “その他” カテゴリー
                        });
                })
                ->get();
            $text .= "【⑥ その他】\n";
            foreach ($others as $task) {
                $text .= "・{$task->title}\n";
            }

            Http::withToken($user->bot_access_token)
                ->post('https://slack.com/api/chat.postMessage', [
                    'channel' => $user->slack_user_id,
                    'text'    => $text,
                ]);
        }
    }

}
