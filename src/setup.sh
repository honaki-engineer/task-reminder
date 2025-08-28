# setup.sh の中身
#!/bin/bash

echo "📦 ロゴ画像を配置中..."

# 必要なディレクトリを作成（なければ）
mkdir -p storage/app/public/images

# setup-assets → storage へ画像をコピー
cp setup-assets/TaskReminder.png storage/app/public/images/

# シンボリックリンク（public/storage）
php artisan storage:link

echo "✅ ロゴ画像の配置が完了しました！"
