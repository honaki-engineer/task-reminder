<?php

namespace App\Support;

final class BackUrlSanitizer
{
    // ----- 画面から「戻り先URL（back_url）」が送られてきたときに「安全なURL」だけを許可する
    public static function sanitize(string $backUrl, string $default, string $appUrl): string
    {
        $backUrl = trim($backUrl);

        // ① 空ならデフォルト
        if($backUrl === '') return $default;

        // ② //evil.com みたいに書くと「今の通信方法(http/https)を勝手に引き継いで外部サイトへ飛ばす」技が使える。
        if(str_starts_with($backUrl, '//')) return $default;

        // ③ 「https://◯◯◯ のような絶対URLは全部ブロック」(自分のサイトは除外)
        if(str_contains($backUrl, '://')) {
            $host    = parse_url($backUrl, PHP_URL_HOST);
            $appHost = parse_url($appUrl,  PHP_URL_HOST);
            return ($host === $appHost) ? $backUrl : $default; // 同一オリジンの絶対URLはここで確定して返す
        }

        // ④ 相対URLだけ許可。先頭に "/" がなければ付ける
        if(!str_starts_with($backUrl, '/')) $backUrl = '/'.$backUrl;

        // ⑤ 自ドメインの絶対URLに正規化して返す
        return rtrim($appUrl, '/').$backUrl;
    }
}
