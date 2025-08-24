<?php

namespace App\Support;

use Carbon\CarbonInterface;

class TaskRules
{
    /**
     * 事実として期限を分類する（TZ基準で日単位判定）
     */
    public static function classifyDueStatus(
            CarbonInterface $endAt,
            CarbonInterface $now
        ): string
    {
        $date = $endAt->toDateString();
        $today = $now->toDateString();

        if($date <  $today) return 'overdue'; // 期限切れ
        if($date === $today) return 'today'; // 今日中
        return 'upcoming'; // 明日以降
    }
}
