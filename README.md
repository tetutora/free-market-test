# 環境構築

1. Dockerを起動する

2. プロジェクト直下で、以下のコマンドを実行する

```
make init
```

## メール認証
mailtrapというツールを使用しています。<br>
以下のリンクから会員登録をしてください。<br>
https://mailtrap.io/

.envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。<br>
MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。

## Stripeについて
コンビニ支払いとカード支払いのオプションがありますが、カード支払いを成功させた場合に意図する画面遷移が行える想定です。<br>

また、StripeのAPIキーは以下のように設定をお願いいたします。
```
STRIPE_PUBLIC_KEY="パブリックキー"
STRIPE_SECRET_KEY="シークレットキー"
```

以下のリンクは公式ドキュメントです。<br>
https://docs.stripe.com/payments/checkout?locale=ja-JP

## テーブル仕様
### usersテーブル
| カラム名                      | 型            | primary key | unique key | not null | foreign key |
| ------------------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id                        | bigint       | ◯           |            | ◯        |             |
| name                      | varchar(255) |             |            | ◯        |             |
| email                     | varchar(255) |             | ◯          | ◯        |             |
| email_verified_at       | timestamp    |             |            |          |             |
| email_verification_hash | varchar(255) |             |            |          |             |
| password                  | varchar(255) |             |            | ◯        |             |
| remember_token           | varchar(100) |             |            |          |             |
| created_at               | timestamp    |             |            |          |             |
| updated_at               | timestamp    |             |            |          |             |


### categoriesテーブル
| カラム名        | 型            | primary key | unique key | not null | foreign key |
| ----------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id          | bigint       | ◯           |            | ◯        |             |
| name        | varchar(255) |             |            | ◯        |             |
| created_at | timestamp    |             |            |          |             |
| updated_at | timestamp    |             |            |          |             |


### productssテーブル
| カラム名        | 型             | primary key | unique key | not null | foreign key |
| ----------- | ------------- | ----------- | ---------- | -------- | ----------- |
| id          | bigint        | ◯           |            | ◯        |             |
| name        | varchar(255)  |             |            | ◯        |             |
| brand\_name | varchar(255)  |             |            | ◯        |             |
| description | text          |             |            | ◯        |             |
| price       | decimal(10,2) |             |            | ◯        |             |
| image       | varchar(255)  |             |            | ◯        |             |
| status      | varchar(255)  |             |            | ◯        |             |
| user\_id    | bigint        |             |            | ◯        | users(id)   |
| is\_sold    | boolean       |             |            | ◯        |             |
| created\_at | timestamp     |             |            |          |             |
| updated\_at | timestamp     |             |            |          |             |

### purchasesテーブル
| カラム名        | 型                            | primary key | unique key | not null | foreign key  |
| ----------- | ---------------------------- | ----------- | ---------- | -------- | ------------ |
| id          | bigint                       | ◯           |            | ◯        |              |
| user\_id    | bigint                       |             |            | ◯        | users(id)    |
| seller\_id  | bigint                       |             |            | ◯        | users(id)    |
| product\_id | bigint                       |             |            | ◯        | products(id) |
| status      | enum('trading', 'completed') |             |            | ◯        |              |
| created\_at | timestamp                    |             |            |          |              |
| updated\_at | timestamp                    |             |            |          |              |


### profilesテーブル
| カラム名             | 型            | primary key | unique key | not null | foreign key |
| ---------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id               | bigint       | ◯           |            | ◯        |             |
| user\_id         | bigint       |             |            | ◯        | users(id)   |
| profile\_picture | varchar(255) |             |            |          |             |
| name             | varchar(255) |             |            | ◯        |             |
| zipcode          | varchar(255) |             |            | ◯        |             |
| address          | varchar(255) |             |            | ◯        |             |
| building         | varchar(255) |             |            |          |             |
| created\_at      | timestamp    |             |            |          |             |
| updated\_at      | timestamp    |             |            |          |             |

### products_categoriesテーブル
| カラム名         | 型         | primary key | unique key | not null | foreign key    |
| ------------ | --------- | ----------- | ---------- | -------- | -------------- |
| id           | bigint    | ◯           |            | ◯        |                |
| product\_id  | bigint    |             |            | ◯        | products(id)   |
| category\_id | bigint    |             |            | ◯        | categories(id) |
| user\_id     | bigint    |             |            | ◯        | users(id)      |
| created\_at  | timestamp |             |            |          |                |
| updated\_at  | timestamp |             |            |          |                |


### commentsテーブル
| カラム名        | 型         | primary key | unique key | not null | foreign key  |
| ----------- | --------- | ----------- | ---------- | -------- | ------------ |
| id          | bigint    | ◯           |            | ◯        |              |
| content     | text      |             |            | ◯        |              |
| product\_id | bigint    |             |            | ◯        | products(id) |
| user\_id    | bigint    |             |            | ◯        | users(id)    |
| created\_at | timestamp |             |            |          |              |
| updated\_at | timestamp |             |            |          |              |


### favoritesテーブル
| カラム名        | 型         | primary key | unique key | not null | foreign key  |
| ----------- | --------- | ----------- | ---------- | -------- | ------------ |
| id          | bigint    | ◯           |            | ◯        |              |
| user\_id    | bigint    |             | ◯          | ◯        | users(id)    |
| product\_id | bigint    |             | ◯          | ◯        | products(id) |
| created\_at | timestamp |             |            |          |              |
| updated\_at | timestamp |             |            |          |              |


### messagesテーブル
| カラム名         | 型           | primary key | unique key | not null | foreign key   |
| ------------ | ----------- | ----------- | ---------- | -------- | ------------- |
| id           | bigint      | ◯           |            | ◯        |               |
| purchase\_id | bigint      |             |            | ◯        | purchases(id) |
| sender\_id   | bigint      |             |            | ◯        | users(id)     |
| is\_read     | boolean     |             |            | ◯        |               |
| body         | text        |             |            | ◯        |               |
| image\_path  | string(255) |             |            |          |               |
| created\_at  | timestamp   |             |            |          |               |
| updated\_at  | timestamp   |             |            |          |               |


### ratingsテーブル
| カラム名         | 型         | primary key | unique key | not null | foreign key   |
| ------------ | --------- | ----------- | ---------- | -------- | ------------- |
| id           | bigint    | ◯           |            | ◯        |               |
| purchase\_id | bigint    |             |            | ◯        | purchases(id) |
| user\_id     | bigint    |             |            | ◯        | users(id)     |
| rating       | tinyint   |             |            | ◯        |               |
| created\_at  | timestamp |             |            |          |               |
| updated\_at  | timestamp |             |            |          |               |


## 使用技術（実行環境）
- Laravel 8.83.29 (PHPフレームワーク)
- MySQL 8.0.42 (データベース)
- Nginx 1.27.3(Web サーバー)
- PHP 8.2.28 (PHP 実行環境)
- Docker (開発環)

## ER図
![alt](./ER図.png)

## テストアカウント
name: ユーザー1
email: user1@example.com
password: password
-------------------------
name: ユーザー2
email: user2@example.com
password: password
-------------------------
-------------------------
name: ユーザー3
email: user3@example.com
password: password
-------------------------

## PHPUnitを利用したテストに関して
以下のコマンド:
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database demo_test;

docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```
※.env.testingにもStripeのAPIキーを設定してください。
