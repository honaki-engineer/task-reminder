<?php

namespace Tests\Unit;

use App\Support\SlackNotifyRules;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class SlackNotifyRulesTest extends TestCase
{
    /**
     * @dataProvider cases
     */
    public function test_category($now, $endAt, $completed, $expected)
    {
        $now = CarbonImmutable::parse($now);
        $end = CarbonImmutable::parse($endAt);

        $this->assertSame($expected, SlackNotifyRules::category($end, $completed, $now));
    }

    public static function cases()
    {
        return [
            'completed => skip' => ['2025-08-23 09:00:00', '2025-08-22 10:00:00', true,  SlackNotifyRules::SKIP],
            'overdue'           => ['2025-08-23 00:00:00', '2025-08-22 23:59:59', false, SlackNotifyRules::OVERDUE],
            'today 00:00'       => ['2025-08-23 00:00:00', '2025-08-23 00:00:00', false, SlackNotifyRules::TODAY],
            'today 23:59'       => ['2025-08-23 00:00:00', '2025-08-23 23:59:59', false, SlackNotifyRules::TODAY],
            'upcoming'          => ['2025-08-23 00:00:00', '2025-08-24 00:00:00', false, SlackNotifyRules::UPCOMING],
        ];
    }
}
