<?php

namespace App\Support;

use Carbon\CarbonInterface;

class SlackNotifyRules
{
    public const OVERDUE  = 'overdue'; // 期限切れ
    public const TODAY    = 'today'; // 本日
    public const UPCOMING = 'upcoming'; // 未来
    public const SKIP     = 'skip';  // 完了は通知しない(完了済み)

    public static function category(
        CarbonInterface $endAt,
        bool $isCompleted,
        CarbonInterface $now): string
    {
        if($isCompleted) return self::SKIP;

        $due = TaskRules::classifyDueStatus($endAt, $now); // overdue/today/upcoming
        return match($due) {
            'overdue' => self::OVERDUE,
            'today'   => self::TODAY,
            'upcoming'   => self::UPCOMING,
        };
    }
}
