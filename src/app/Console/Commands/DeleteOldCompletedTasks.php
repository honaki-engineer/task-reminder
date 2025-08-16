<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class DeleteOldCompletedTasks extends Command
{
    protected $signature = 'tasks:delete-old-completed';
    protected $description = '完了済みで締切が昨日以前のタスクを削除する';

    public function handle()
    {
        $yesterday = Carbon::yesterday()->endOfDay();

        $deleted = Task::where('is_completed', true)
            ->where('end_at', '<=', $yesterday)
            ->delete();

        $this->info("{$deleted} 件の古い完了タスクを削除しました。");
    }
}
