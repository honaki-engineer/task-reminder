<?php

namespace Tests\Unit;

use App\Support\TaskRules;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class DueStatusClassifierTest extends TestCase
{
    /**
     * @dataProvider cases
     */
    public function test_classify_due_status($now, $endAt, $expected)
    {
        $now  = CarbonImmutable::parse($now);
        $end  = CarbonImmutable::parse($endAt);

        $this->assertSame($expected, TaskRules::classifyDueStatus($end, $now));
    }

    public static function cases()
    {
        return [
            'overdue (yesterday 23:59)'     => ['2025-08-23 00:00:00', '2025-08-22 23:59:59',       'overdue'],
            'today boundary (00:00)'        => ['2025-08-23 00:00:00', '2025-08-23 00:00:00',       'today'],
            'today end (23:59:59)'          => ['2025-08-23 00:00:00', '2025-08-23 23:59:59',       'today'],
            'upcoming boundary (tomorrow)'  => ['2025-08-23 00:00:00', '2025-08-24 00:00:00',       'upcoming'],
        ];
    }
}
