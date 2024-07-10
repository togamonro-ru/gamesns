<?php
    //ユーザー名
    $user = "root";
    //パスワード
    $pass = "";
    //データベース名
    $database = "game_sns";
    //サーバー
    $server = "localhost:3308";

    //DSN文字列の生成
    $dsn = "mysql:host={$server};dbname={$database};charset=utf8";

    //mysqlデータベースへの接続
    try {
        //PDOのインスタンスを作成し、DBへ接続する
        $pdo = new PDO($dsn,$user,$pass);
        //プリペアドステートメントのエミュレーションを無効化
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        //例外がスローされる設定にする
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //echo "データベースに接続しました";
    }catch(Exception $e){
        //エラー時の処理
        echo "DB接続エラー";
        echo $e->getMessage();
        exit();
    }