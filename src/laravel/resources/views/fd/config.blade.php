<?php 
// データベースの接続情報
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'pcon');
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');
// セッション開始
session_start();
// セッションハイジャック対策...同じセッションでも接続するごとに別のセッションIDが生成され使用される。
session_regenerate_id(true);
// headerエラー対策
ob_start();
// mb_系の関数で不具合起きないようおまじない。
mb_internal_encoding("UTF-8");


// 変数の初期化
$pdo = null;      // PDO用変数
$stmt = null;     // fetch処理用
$res = null;      // レスポンス

// データベースに接続
try {
  $tmp = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
  );
  $pdo = new PDO('mysql:charset=UTF8;dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS, $tmp);
} catch (PDOException $e) {
  // 接続エラーのときエラー内容を取得する
  $error_message[] = $e->getMessage();
}

// 日時を取得
$current_date = date("Y-m-d H:i:s");
?>