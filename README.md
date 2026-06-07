# flea_market_app

フリマアプリ（Laravel 8）の学習用プロジェクトです。  
Docker でアプリ、DB、Web サーバー、メール確認環境までまとめて起動できます。

## できること

- 会員登録 / ログイン / ログアウト
- メール認証
- 商品一覧 / 商品詳細
- いいね / コメント
- 商品出品
- 商品購入（Stripe Checkout）
- 送付先住所の登録・変更
- マイページ（出品一覧 / 購入一覧）
- プロフィール編集

## 技術スタック

- PHP 8 系（Laravel 8.75）
- MySQL 8.0.26
- Nginx
- Docker / Docker Compose
- PHPUnit 9
- Stripe PHP SDK
- MailHog

## 画面・接続先

- アプリ: http://localhost
- phpMyAdmin: http://localhost:8080
- MailHog: http://localhost:8025

## ディレクトリ構成（主要部分）

```text
flea_market_app/
	docker/                 # Docker 関連設定
	src/                    # Laravel アプリ本体
		app/
		config/
		database/
		resources/
		routes/
		tests/
	docker-compose.yml
```

## セットアップ手順

### コマンド表記の見方

- `bash` と書かれたブロックは、ターミナルで実行するコマンドです。
- 1行ずつコピーして実行すれば大丈夫です。

### 1) コンテナ起動

```bash
docker-compose up -d --build
```

### 2) PHP コンテナに依存パッケージをインストール

```bash
docker-compose exec php composer install
```

### 3) 環境変数ファイルを作成

```bash
cp src/.env.example src/.env
```

### 4) `.env` を Docker 構成に合わせて編集

`src/.env` は `src/.env.example` をコピーして作っています。  
まずは以下の行を `src/.env` で書き換えてください。

- DB 設定: `src/.env.example` の 11〜16行目
- Mail 設定: `src/.env.example` の 31〜38行目

DB 設定は次の値に変更します。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

Mail 設定は次の値で動作します（通常はこのままでOK）。

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

Stripe 設定は `src/.env.example` には項目がないため、`src/.env` の末尾に追記してください。  
購入機能の Stripe 決済を試さない場合は、未設定でもセットアップ自体は進められます。

```env
STRIPE_SECRET_KEY=your_secret_key
STRIPE_PUBLISHABLE_KEY=your_publishable_key
```

### 5) アプリキー生成

```bash
docker-compose exec php php artisan key:generate
```

### 6) マイグレーション

```bash
docker-compose exec php php artisan migrate
```

### 7) シーダー実行（デモデータ投入）

カテゴリとダミーデータを入れる場合は、続けて以下を実行してください。

```bash
docker-compose exec php php artisan db:seed
```

※ データを一度まっさらにして、マイグレーションとシーディングをまとめてやり直したい場合は以下でもOKです。

```bash
docker-compose exec php php artisan migrate:fresh --seed
```

### 8) 動作確認

ブラウザで http://localhost にアクセスしてください。

## テスト実行

```bash
docker-compose exec -T php php artisan test
```

## よく使うコマンド

```bash
# コンテナ停止
docker-compose down

# コンテナ再起動
docker-compose restart

# ルート一覧確認
docker-compose exec php php artisan route:list

# DB 初期化（全削除して再作成）
docker-compose exec php php artisan migrate:fresh

# DB 初期化 + シーダー実行
docker-compose exec php php artisan migrate:fresh --seed
```

## 補足

- Laravel のデフォルト README は `src/README.md` にあります。
- 本プロジェクトで実際に使う手順は、このルート README を参照してください。
