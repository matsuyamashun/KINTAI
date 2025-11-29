<h1>coachtech 勤怠管理アプリ</h1>

<h2>概要</h2>
・ある企業が開発した独自の勤怠管理アプリの設計、コーディング、テストの実施

<h2>環境構築</h2>

**Dockerビルド**

1.git clone  

Http版はっておきます　　https://github.com/matsuyamashun/furima.git

2.DockerDesktopアプリ立ち上げ

3.docker-compose up -d --build

MacのM1・M2チップのPCの場合、no matching manifest for linux/arm64/v8 in the manifest list entriesのメッセージが表示されビルドができないことがあります。 エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください

**Laravel環境構築**

1.docker-compose exec php bash

2.composer install

3..env.exampleファイルを.envに変更。または、PHP内にて**cp .env.example .env**コマンドで新しく作成

4..envファイルの変更

**DB_CONNECTION=mysql**

**+　DB_HOST=mysql**

**DB_PORT=3306**

**+DB_DATABASE=laravel_db**

**+DB_USERNAME=laravel_user**

**+DB_PASSWORD=laravel_pass**

使用するブラウザによって違うかもしれないので違う場合はその都度変更おねがいします

5.アプリケーションキー作成

php artisan key:generate

6.マイグレーション実行

php artisan migrate

7.シーティングの実行

php artisan db:seed
