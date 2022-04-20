<?php

include("../_inc/config.php"); // cnofigを展開。


/*============
★SELECT


各種データ取得
============*/
if (!empty($pdo)) {
  // fd_console_dialのデータを取得
  $sql = "SELECT * FROM fd_console_dial ORDER BY id DESC";
  $stmt = $pdo->query($sql);
  $fd_console_dial = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*============
★SELECT

【照会】
押下時の処理
============*/
if (!empty($pdo) && !empty($_POST['traffic_form_submit'])) {


  if ($_POST['traffic_start_datetime'][1] !== '') {
    $start_time = $_POST['traffic_start_datetime'][1] . ':00';
  } else {
    $start_time = '00:00:00';
  }
  if ($_POST['traffic_end_datetime'][1] !== '') {
    $end_time = $_POST['traffic_end_datetime'][1] . ':59';
  } else {
    $end_time = '23:59:59';
  }

  $start_datetime = $_POST['traffic_start_datetime'][0] . ' ' . $start_time;
  $end_datetime = $_POST['traffic_end_datetime'][0] . ' ' . $end_time;

  try {
    // トランザクション開始
    $pdo->beginTransaction();
    $sql = "
    SELECT * FROM original_trweb_values WHERE 
    service_number = :service_number AND 
    target_datetime >= :start_datetime AND 
    target_datetime < :end_datetime 
    ";
    if (!empty($_POST['traffic_dial_place'])) { // 都道府県の入力があれば SQLを追記。
      $sql .= "AND dial_place LIKE :dial_place ";
    }
    $sql .= "
    ORDER BY target_datetime DESC;
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':service_number', $_POST['traffic_service_number'], PDO::PARAM_STR);
    $stmt->bindParam(':start_datetime', $start_datetime, PDO::PARAM_STR);
    $stmt->bindParam(':end_datetime', $end_datetime, PDO::PARAM_STR);
    //$stmt->bindValue(':user_password', $user_password, PDO::PARAM_STR);
    if (!empty($_POST['traffic_dial_place'])) { // 都道府県の入力があれば bindを追記。
      $tmp = "%{$_POST['traffic_dial_place']}%";
      $stmt->bindParam(':dial_place', $tmp, PDO::PARAM_STR);
    }
    $stmt->execute();
    $traffic_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($traffic_result) === 0) {
      $traffic_result = [];
      $traffic_result[] = array('result' => 'notfound');
      $error_msg = "見つかりませんでした。";
    }
    // プリペアドステートメントを削除
    $stmt = null;
  } catch (PDOException $e) {
    $traffic_result = [];
    $traffic_result[] = array('result' => 'notofound');
    $error_msg = "見つかりませんでした。";
  }
  $traffic_inquiry = $_POST;
} else {
  $traffic_result = [];
  $traffic_result[] = array('result' => 'notofound');
}


//echo '<pre>';
//echo var_dump($_POST);
//echo '</pre>';




//----------------------------------------------------------------------------------------
/**
 * @param {string}$file_name CSVの名称。
 * @param {array}$data 出力する配列。
 * @param {string}$encoder エンコード情報。"sjis"でSJIS出力。省略可。
 * @param {bool}$datetime trueで日時付き出力する。省略可
 */
function Csv_Export($file_name, $data, $encoder = "utf8", $datetime = false)
{
  // datetimeが trueならファイル名に日時を付与。
  if ($datetime === true) {
    // 書き込み日時を取得
    $current_date = date("YmdHis");
    $file_name .= "_{$current_date}";
  }
  $fp = fopen('php://output', 'w');
  // エンコーダーの引数がSJISなら変換処理を挟む。
  if ($encoder === "sjis") {
    stream_filter_prepend($fp, 'convert.iconv.utf-8/cp932');
  }
  foreach ($data as $row) {
    fputcsv($fp, $row, ',', '"');
  }
  fclose($fp);
  header('Content-Type: application/octet-stream');
  header("Content-Disposition: attachment; filename={$file_name}");
  header('Content-Transfer-Encoding: binary');
  exit;
}

//----------------------------------------------------------------------------------------
/*============


ページ情報保管
============*/
if (!empty($_GET['page'])) {
  $_SESSION['fd_traffic_page_state'] = 'fd_traffic_' . $_GET['page'];
} else {
  if (empty($_SESSION['fd_traffic_page_state'])) {
    $_SESSION['fd_traffic_page_state'] = 'fd_traffic_home';
  }
}



//----------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <link rel="icon" type="image/png" href="">
  <title>Pcon トラフィックメニュー</title>

  <?php include("../_inc/head.php"); // headを展開。
  ?>

  <link rel="stylesheet" href="../css/style.css">
</head>

<body>


  <!--===================================================


    ローディング画面
  ====================================================-->
  <div id="loading_window" style="background: #e8ecf2; text-align: center; padding: 46vh 0 0 0;">
    <div class="spinner-grow text-danger" role="status">
    </div>
    <div class="spinner-grow text-primary" role="status">
    </div>
    <div class="spinner-grow text-warning" role="status">
    </div>
    <span class="sr-only">Loading...</span>
  </div>

  <a href="../fd-console/">FD管理コンソール</a>




  <form method="post" id="fd_traffic_form" onsubmit="return fd_traffic_validation()">
    <div>開始日：
      <input type="text" id="traffic_start_date" name="traffic_start_datetime[]" class="form-control">
      開始時刻：
      <input type="text" id="traffic_start_time" name="traffic_start_datetime[]" class="timepicker form-control" data-time-format="H:i">
    </div>
    <div>～終了日：
      <input type="text" id="traffic_end_date" name="traffic_end_datetime[]" class="form-control">
      ～終了時刻：
      <input type="text" id="traffic_end_time" name="traffic_end_datetime[]" class="timepicker form-control" data-time-format="H:i">
    </div>
    <div>サービス番号：
      <select id="traffic_service_number" name="traffic_service_number" class="form-control">
      </select>
      都道府県：
      <input type="text" id="traffic_dial_place" name="traffic_dial_place" placeholder="🔍都道府県を入力" class="form-control" style="width: 200px;">
    </div>
    <div>　
      <input type="submit" value="照会" name="traffic_form_submit" class="form-control" style="margin: 0 0 0 20px;">
    </div>
    <div>　
      <input type="button" value="CSV出力（SJIS）" id="traffic_csv_export_sjis" data-encoding="SJIS" class="form-control" style="margin: 0 0 0 20px;">
      <input type="button" value="CSV出力（UTF8）" id="traffic_csv_export_utf8" data-encoding="UTF8" class="form-control" style="margin: 0 0 0 20px;">
    </div>
  </form>



  <br><br><br>
  <div id="jsGrid"></div>

  <br><br><br>
  <?php
  /*
  echo '<pre>';
  echo var_dump($traffic_result);
  echo '</pre>';
  */
  ?>
  <!-----------------------------------------------------
  JavaScript読み込み
  ------------------------------------------------------>
  <script>
    const fd_console_dial = <?php echo json_encode($fd_console_dial); ?>;
    const traffic_result = <?php echo json_encode($traffic_result); ?>;
    const fd_traffic_page_state = "<?php echo $_SESSION['fd_traffic_page_state']; ?>";
    const post_param = <?php echo json_encode($_POST); ?>;
    console.log(post_param);
  </script>
  <script src="../js/mybrary.js"></script>
  <script src="../_inc/config.js"></script>
  <script src="../js/script.js"></script>
</body>

</html>