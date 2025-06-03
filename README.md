# time_attendance_management_app(模擬試験勤怠管理アプリ)

## 環境構築
Dockerビルド
1. git clone git@github.com:hirata784/time_attendance_management_app.git
2. DockerDesktopアプリを立ち上げる
3. cd time_attendance_management_app
4. docker-compose up -d --build

＊MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集して下さい。

## Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
4. .envに以下の環境変数を変更する
``` text
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
MAIL_HOST=mail
MAIL_FROM_ADDRESS=info@example.com
```
5. アプリケーションキーの作成
``` bash
php artisan key:generate
```
6. マイグレーションの実行
``` bash
php artisan migrate
```
7. シーディングの実行
``` bash
php artisan db:seed
```
8. シンボリックリンクの作成
``` bash
php artisan storage:link
```

## 単体テスト
テスト用データベースの準備
1. PHPコンテナ上にいる場合、「exit」で抜ける
2. docker-compose exec mysql bash
3. mysql -u root -p
4. パスワードを求められるので「root」と入力しEnter
5. CREATE DATABASE demo_test;

テスト用の.envファイルの作成
1. MySQLコンテナ上にいる場合、「exit」で抜ける
2. docker-compose exec php bash
3. cp .env .env.testing
4. .env.testingの環境変数を変更する
``` text
APP_ENV=test
APP_KEY=
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```
5. php artisan key:generate --env=testing
6. php artisan config:clear
7. php artisan migrate --env=testing

メール認証の仕方
1. 会員登録画面より会員登録後、メール認証画面へ移動する
2. [認証はこちらから]を押下し、mailhogを開く
    ※下記URL欄のmailhogから、mailhogを直接開いても良いです
3. 登録したメールアドレスが記載されている本文をクリックする
4. [Verify Email Address]をクリックする  
時間内にクリック出来なかった場合、認証画面の[認証メールを再送する]を  
クリックして下さい。認証メールが再送されます。
5. メール認証が完了し、プロフィール編集画面へ移動する

## ダミーデータ
1. ユーザー情報  
下記テストアカウントが作成されます
2. 勤怠記録情報  
一般ユーザーの出勤・退勤・休憩時間が作成されます。  
期間は2025年4月と2025年5月の2ヶ月分です。  
承認待ち・承認済み状態のデータはありません。


## テストアカウント
一般ユーザー  
name: テスト一郎  
email: ichiro@example.com  
password: ichiichi  
-------------------------
一般ユーザー  
name: テスト二郎  
email: jiro@example.com  
password: jirojiro  
-------------------------
一般ユーザー  
name: テスト三郎  
email: saburo@example.com  
password: sabusabu  
-------------------------
管理者  
name: テスト四郎  
email: shiro@example.com  
password: shiroshiro  
-------------------------

## CSVダウンロード
スタッフ別勤怠一覧画面(管理者)より、CSV出力機能があります。  
[CSV出力]をクリックすると、CSVファイルがダウンロードフォルダへ保存されます。  
ファイル名は[(選択年月)(選択ユーザー名).csv]です。

## 使用技術
- PHP 7.4.9
- Laravel 8.83.29
- MySQL 8.0.26

## ER図
![画像](https://coachtech-lms-bucket.s3.ap-northeast-1.amazonaws.com/question/20250519150517_time_attendance_management_app.png)

## URL
- 開発環境：http://localhost/
- 会員登録画面：http://localhost/register
- 一般ユーザーログイン画面：http://localhost/login
- 管理者ユーザーログイン画面：http://localhost/admin/login
- phpMyAdmin：http://localhost:8080/
- mailhog：http://localhost:8025/