# free-market

## Dockerビルド
- ・git clone https://github.com/tetutora/coachtech-test
- ・docker-compose up -d --build
- ・php artisan db:seed --class=UserSeeder
- ・php artisan db:seed

## Laravel環境構築
- ・docker-compose exec php bash
- ・composer install
- ・cp .env.example .env　環境変数を適宜変更
- ・php artisan key:generate
- ・php artisan migrate
- ・php artisan db:seed

## 開発環境
- ・商品一覧画面：http://localhost
- ・会員登録画面：http://localhost/rejister
- ・phpMyAdmin：http://localhost:8080/


## 使用技術（実行環境）
- ・Laravel 8.83.29 (PHP Laravel)
- ・MySQL 8.0.26 (データベース)
- ・Nginx 1.21.1(Web サーバー)
- ・PHP 8.3.13 (PHP 実行環境)
- ・Docker (開発環境のコンテナ管理)

## ER図

![表示](./test.drawio.svg)

