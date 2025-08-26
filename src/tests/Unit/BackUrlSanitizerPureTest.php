<?php

namespace Tests\Unit;

use App\Support\BackUrlSanitizer;
use PHPUnit\Framework\TestCase;

class BackUrlSanitizerPureTest extends TestCase
{
    /**
     * @dataProvider cases
     */
    public function test_sanitize(string $given, string $expected): void
    {
        $appUrl  = 'https://example.com';
        $default = 'https://example.com/tasks';

        $this->assertSame(
            $expected === '__default__' ? $default : $expected,
            BackUrlSanitizer::sanitize($given, $default, $appUrl)
        );
    }

    public static function cases(): array
    {
        return [
            // ① 空/未指定相当
            'empty'                => ['', '__default__'],

            // ② プロトコル相対はブロック
            'protocol-relative'    => ['//evil.com/hack', '__default__'],

            // ③ 絶対URL：外部はブロック、内部は許可（http/https差は許容）
            'external absolute'    => ['https://evil.com/attack', '__default__'],
            'internal https'       => ['https://example.com/a?b=1', 'https://example.com/a?b=1'],
            'internal http'        => ['http://example.com/mixed',  'http://example.com/mixed'],

            // ④相対URLは自ドメインで絶対化
            'relative with slash'  => ['/tasks/list?tab=me', 'https://example.com/tasks/list?tab=me'],
            'relative no slash'    => ['tasks/list',         'https://example.com/tasks/list'],

            // スキーム風文字列（://無し）はパスとして扱われる
            'javascript-like'      => ['javascript:alert(1)', 'https://example.com/javascript:alert(1)'],
        ];
    }
}
