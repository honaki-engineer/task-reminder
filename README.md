# TaskReminder

## 概要

毎日のタスクを **マトリクス分類 × 毎朝リマインド（Slack通知）** で“忘れない”。シンプルな個人用タスクリマインダー。

---

## サイト

🔗 アプリ：<https://task-reminder.akkun1114.com/>  
🔗 ゲストログイン（今すぐ試せます）：<https://task-reminder.akkun1114.com/guest-login?token=guest123>  

---

## 目次

- [サイト](#サイト)
- [使用技術](#使用技術)
- [主な機能](#主な機能)
- [クイックスタート-ローカル](#クイックスタート-ローカル)
- [ディレクトリ構成](#ディレクトリ構成)
- [本番環境の注意点](#本番環境の注意点)
- [テスト](#テスト)
  
---

## 使用技術

Laravel 9.x / PHP 8.2 / MariaDB 10.5 / Docker Compose / Node.js 22.x（ローカル） / 16.20.2（本番：nodebrew） / Tailwind / Xserver（本番）

---

## 主な機能

- **認証/認可**：Breeze、全ルート `auth` / 取得は本人スコープ固定  
- **タスク**：CRUD / 完了トグル / 締切昇順・キーワード検索 / 1日ビュー（`start_at` が今日以前）  
- **Slack**：OAuth v2（`chat:write`, `channels:read`, `users:read`） / 毎朝リマインド（Slack通知） / ON/OFF・解除  
- **バリデーション**：`TaskRequest` で `start_at/end_at` 合成、`H:i`、`end_at ≥ start_at`  
- **セキュリティ**：`back_url` は自ドメインのみ許可（`//evil.com` & 外部絶対URLはデフォルトに退避）  
- **自動クリーンアップ**：「完了済み × 締切が昨日より前」を毎日削除（Scheduler / Cron）  
- **その他**：400〜503 カスタムエラーページ / テスト（Feature・Unit）  

---

## クイックスタート-ローカル

1. リポジトリをクローン
```bash
git clone https://github.com/honaki-engineer/task-reminder.git
cd task-reminder/src
```
2. 環境変数を設定
```bash
cp .env.example .env
```
3. PHPパッケージをインストール
```bash
composer install
```
4. アプリケーションキーを生成
```bash
php artisan key:generate
```
5. DBマイグレーション & 初期データ投入
```bash
php artisan migrate --seed
```
6. フロントエンドビルド（Tailwind/Vite 使用時）
```bash
npm install
npm run dev  # 開発環境用
npm run build  # 本番環境用
```
7. サーバー起動（ローカル開発用）
```bash
php artisan serve
```

### .env 設定例（ローカル開発用）

.env の `DB_` 各項目などは、Xserver またはローカルの環境に応じて適宜変更してください。

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_reminder
DB_USERNAME=root
DB_PASSWORD=

# Slack OAuth
SLACK_CLIENT_ID=xxxxxxxxx.aaaaaaaaaaaa
SLACK_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxx
SLACK_REDIRECT_URI=https://<your-domain>/slack/callback
```

※ Slack連携〜毎朝通知の構成まとめ：<https://qiita.com/honaki/items/32568fda4ea3cb5a3447>

---

## ディレクトリ構成

<details><summary>ディレクトリ構成（抜粋） </summary>

```txt
task-reminder/
├── docker-compose.yml
├── docker/                 # ローカル開発（Apache/PHP/MariaDB/phpMyAdmin/Mailpit）
└── src/
    ├── app/
    │   ├── Console/         # Scheduler（毎朝Slack/自動削除）
    │   ├── Http/
    │   │   ├── Controllers/ # Task/Slack 等
    │   │   └── Requests/    # TaskRequest（相関バリデーション）
    │   ├── Models/          # Task / TaskCategory / SlackNotification / User
    │   ├── Services/        # TaskService（処理を集約し、コントローラーを簡潔に保つ）
    │   └── Support/         # 戻り先URLサニタイズ
    ├── database/{migrations,seeders}
    ├── lang/ja/             # 日本語メッセージ
    ├── resources/views/     # tasks/*, slacks/*, errors/*, emails/*
    ├── routes/web.php
    └── tests/{Feature,Unit}
```

</details>

---

## 本番環境の注意点

Xserver 上で Laravel アプリを本番公開する際の詳細な手順（SSH 接続、`.env` 設定、`.htaccess` 配置、`index.php` 修正、ビルドファイルの配置など） のまとめ：

- ローカル（Docker） & エックスサーバー（サブドメイン） の場合  
  <https://qiita.com/honaki/items/abf3f8cba40f5b9a2e3b>

---

## テスト
- **Feature**：タスクCRUD / 完了トグル / 認可（他人データは404） / バリデーション / Slack 連携・通知トグル
- **Unit**：`back_url` サニタイズ（外部絶対URL や プロトコル相対URL等の拒否）

```bash
cd task-reminder/src
php artisan test
```