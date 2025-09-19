# TaskReminder

## 概要

「タスクを忘れがち」「書き出しても確認しない」「スプレッドシートやメモアプリでは管理が煩雑」——  
そんな日々の小さなモヤモヤを解決。  
毎日のタスクを **マトリクス分類** × **毎朝リマインド（Slack通知）** で忘れない  
シンプルな個人用タスクリマインダー（Laravel 製 Web アプリ）です。  

---

## サイト

- アプリ  
  https://task-reminder.akkun1114.com/  
- ゲストログイン（今すぐ試せます）  
  https://task-reminder.akkun1114.com/guest-login?token=guest123  

### ゲストログイン情報
- メールアドレス：不要
- パスワード：不要

上記のURLをクリックするだけで、ゲストログインが完了します。

---

## 目次

- [概要](#概要)
- [サイト](#サイト)
- [使用技術](#使用技術)
- [主な機能](#主な機能)
- [セットアップ手順(開発環境)](#セットアップ手順開発環境)
- [ディレクトリ構成](#ディレクトリ構成)
- [テスト](#テスト)
- [本番環境の注意点](#本番環境の注意点)
  
---

## 使用技術

- **フロントエンド**：HTML / JavaScript / Tailwind CSS
- **バックエンド**：PHP 8.x（開発: 8.2.29 / 本番: 8.2.28） / Laravel 9.52.20  
- **データベース**：MariaDB 10.5（開発 / 本番・共にMySQL互換）  
- **インフラ・環境**：Docker（Compose v2 必須） / Xserver / macOS Sequoia 15.3.1  
- **ビルド環境**：Node.js 24.4.0（開発） / Node.js 16.20.2（本番: Xserver に nodebrew で導入） / Composer 2.8.x（開発: 2.8.11 / 本番: 2.8.5）  
- **開発ツール**：VSCode / Git / GitHub / phpMyAdmin  
  
※ ローカル開発環境は、Node.js 24.4.0 を使用してビルドを実行しています。  
本番環境（Xserver）は、nodebrew を利用して Node.js 16.20.2 を導入し、ビルドを行っています。  
なお、Xserver では Node.js の標準提供は行われていないため、サーバー内ビルドは公式サポート対象外の構成となります。  
必要に応じて、ローカルビルド済みのファイルをアップロードする運用をおすすめいたします。

---

## 主な機能
### 開発者目線

- **認証/認可**：Breeze、全ルート `auth` / 取得は本人スコープ固定  
- **タスク**：CRUD / 完了トグル / 締切昇順・キーワード検索 / 1日ビュー（`start_at` が今日以前）  
- **Slack**：OAuth v2（`chat:write`, `channels:read`, `users:read`） / 毎朝リマインド（Slack通知） / ON/OFF・解除  
- **バリデーション**：`TaskRequest` で `start_at/end_at` 合成、`H:i`、`end_at ≥ start_at`  
- **セキュリティ**：`back_url` は自ドメインのみ許可（`//evil.com` & 外部絶対URLはデフォルトに退避）  
- **自動クリーンアップ**：「完了済み × 締切が昨日より前」を毎日削除（Scheduler / Cron）  
- **その他**：400〜503 カスタムエラーページ / テスト（Feature・Unit）  

### ユーザー目線
#### 区分別 機能対応表

| 機能                                  | 非ログインユーザー | 一般ユーザー       |
| -------------------------------------| --------------- | ---------------- |
| ログイン                               | -               | ●                |
| パスワード再発行                        | ●               | ●                |
| ゲストログイン（1クリック）               | ●               | -                |
| タスクのCRUD                           | -               | ●                |
| 「フリー検索」による領収書検索（一覧ページ） | -               | ●                |
| タスク完了切り替え（詳細ページ）           | -               | ●                |
| OneDayタスク表示                        | -               | ●               |
| Slack連携                              | -               | ●                |
| Slack通知切り替え（Slack連携後のみ）       | -               | ●                |
| Slack毎朝リマインド（Slack連携後のみ）      | -               | ●                |
| プロフィール編集                         | -               | ●                |

---

## セットアップ手順（開発環境）

1. リポジトリをクローン
```bash
git clone https://github.com/honaki-engineer/task-reminder.git
cd task-reminder
```
2. Dockerコンテナ起動  
`docker-compose.yml`のあるディレクトリでコマンド実行
```bash
# 初回起動
docker compose up -d --build

# 2回目以降
docker compose up -d
```
3. Dockerコンテナに入る
```bash
docker compose exec app bash
```
4. 環境変数を設定
```bash
cp .env.example .env
```
.env の `DB_` 各項目などは、開発環境に応じて適宜変更してください。  
- [.env 設定例（開発環境）](#env-設定例開発環境)
5. PHPパッケージをインストール
```bash
composer install
```
6. アプリケーションキーを生成
```bash
php artisan key:generate
```
7. DBマイグレーション & 初期データ投入
```bash
php artisan migrate --seed
```
8. フロントエンドビルド（Tailwind/Vite 使用時）
```bash
exit # Docker コンテナを抜ける

pwd # ~/task-reminder
cd ./src
npm install
npm run dev
```
9. 初期画像作成（ストレージリンク作成含む）
```bash
chmod +x setup.sh
./setup.sh
```
10. Slack連携〜毎朝通知の構成  
  https://qiita.com/honaki/items/32568fda4ea3cb5a3447
11. アクセス
- アプリ  
  http://localhost:8080  
- phpMyAdmin  
  http://localhost:8081  
  
  
### .env 設定例（開発環境）

```env
APP_NAME=TaskReminder
APP_ENV=local
APP_DEBUG=true
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_HOST=db # Compose サービス名
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mailpit を使う場合
MAIL_MAILER=smtp
MAIL_HOST=mailpit # Docker
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Slack OAuth（セットアップ９で解説）
SLACK_CLIENT_ID=
SLACK_CLIENT_SECRET=
SLACK_REDIRECT_URI=
```

---

## ディレクトリ構成

```txt
task-reminder/
├── docker-compose.yml
├── docker/                                   # ローカル開発（Apache/PHP/MariaDB/phpMyAdmin/Mailpit）
└── src/
    ├── app/
    │   ├── Console/                          # Scheduler（毎朝Slack/自動削除）
    │   ├── Http/
    │   │   ├── Controllers/                  # 各種コントローラ
    │   │   └── Requests/                     # フォームリクエスト + 相関バリデーション
    │   ├── Models/                           # Eloquent モデル
    │   ├── Services/                         # サービスクラス
    │   └── Support/                          # 戻り先URLサニタイズ
    ├── config/app.php                        # アプリ全体設定 + ゲストログインENV（guest_token 等）
    ├── database/
    │   ├── factories/                        # ファクトリーファイル
    │   ├── migrations/                       # マイグレーションファイル
    │   └── seeders/                          # 初期データ投入用
    ├── lang/ja/                              # バリデーションエラーの日本語化など
    ├── public/
    │   ├── index.php                         # エントリーポイント
    │   └── storage -> ../storage/app/public  # storage:link のシンボリックリンク
    ├── resources/
    │   ├── css/                              # Tailwind CSS定義
    │   ├── js/                               # JavaScriptエントリーポイント
    │   └── views/                            # Bladeテンプレート
    ├── routes/web.php                        # ルーティング設定
    ├── setup-assets/                         # 初期画像格納
    ├── storage/app/public/images/            # setup-assets/ の保存先
    ├── tests/                                # テスト
    │   ├── Feature/                          # Featureテスト
    │   └── Unit/                             # Unitテスト
    ├── .env.example                          # 環境変数テンプレート
    ├── composer.json                         # PHPパッケージ管理ファイル
    ├── package.json                          # Node.jsパッケージ管理ファイル
    ├── README.md
    ├── setup.sh                              # 初期画像のセットアップ 
    ├── tailwind.config.js                    # Tailwind CSS 設定
    └── vite.config.js                        # Vite 設定
```

---

## テスト
- **Feature**：タスクCRUD / 完了トグル / 認可（他人データは404） / バリデーション / Slack連携・通知トグル
- **Unit**：`back_url` サニタイズ（外部絶対URL や プロトコル相対URL等の拒否）

### テスト手順
1. [セットアップ手順 8. フロントエンドビルド](#セットアップ手順開発環境)を実行  
2. [セットアップ手順 2. Dockerコンテナ起動](#セットアップ手順開発環境)を実行  
3. [セットアップ手順 3. Dockerコンテナに入る](#セットアップ手順開発環境)を実行  
4. テスト実行
```bash
php artisan test
```

---

## 本番環境の注意点

Xserver 上で Laravel アプリを本番公開する際の詳細な手順（SSH 接続、`.env` 設定、`.htaccess` 配置、`index.php` 修正、ビルドファイルの配置など）は、以下の記事にまとめています：  

- エックスサーバー（Docker & メインドメイン）でデプロイ手順  
  https://qiita.com/honaki/items/7f88d2b516bfa6ee9368  

- エックスサーバー（Docker & サブドメイン）でデプロイ手順  
  https://qiita.com/honaki/items/abf3f8cba40f5b9a2e3b  