<?php
/// Access-Control-Allow-Originエラーを回避する
header("Access-Control-Allow-Origin: *");

include("../_inc/config.php"); // cnofigを展開。

/*============
★SELECT


各種データ取得
============*/
if (!empty($pdo)) {
  // fd_console_companyのデータを取得
  $sql = "SELECT * FROM fd_console_company ORDER BY id DESC";
  $stmt = $pdo->query($sql);
  $fd_console_company = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // fd_console_dialのデータを取得
  $sql = "SELECT * FROM fd_console_dial ORDER BY id DESC";
  $stmt = $pdo->query($sql);
  $fd_console_dial = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // fd_console_baseのデータを取得
  $sql = "SELECT * FROM fd_console_base ORDER BY id DESC";
  $stmt = $pdo->query($sql);
  $fd_console_base = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // fd_console_dialbaseのデータを取得
  $sql = "SELECT * FROM fd_console_dialbase ORDER BY id DESC";
  $stmt = $pdo->query($sql);
  $fd_console_dialbase = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//var_dump($fd_console_base);

//----------------------------------------------------------------------------------------
/*============
★INSERT
fd_console_form_submitがなされていれば
fd_console_dialにレコード追加。
baseに入力があればfd_console_dialbaseにも追加する。
============*/
if (!empty($_POST['fd_console_edit_submit']) && !empty($pdo)) {
  // ===> fd_console_dialへの追加 ===>
  // トランザクション開始
  $pdo->beginTransaction();
  try {
    // SQL作成
    $sql = '';
    $sql .= 'INSERT INTO fd_console_dial (company_type, service_type, service_name, service_number, start_datetime, end_datetime, update_datetime) VALUES ';
    $sqlarr = [];
    for ($i = 0; $i < count($_POST['fd_form']['company_type']); $i++) {
      $tmparr = [];
      $tmparr[] = $_POST['fd_form']['company_type'][$i];
      $tmparr[] = $_POST['fd_form']['service_type'][$i];
      $tmparr[] = $_POST['fd_form']['service_name'][$i];
      $tmparr[] = $_POST['fd_form']['service_number'][$i];
      $tmparr[] = $_POST['fd_form']['start_datetime'][$i];
      $tmparr[] = $_POST['fd_form']['end_datetime'][$i];
      if (array_search('', $tmparr) === false) {
        $sqlarr[] = "(:company_type{$i}, :service_type{$i}, :service_name{$i}, :service_number{$i}, :start_datetime{$i}, :end_datetime{$i}, :update_datetime{$i})";
      }
    }
    $sql .= implode(",", $sqlarr);
    //$sql .= '(:company_type, :service_type, :service_name, :service_number, :start_datetime, :end_datetime, :update_datetime)';

    $stmt = $pdo->prepare($sql);


    $counter = 0;
    for ($i = 0; $i < count($_POST['fd_form']['company_type']); $i++) {
      $tmparr = [];
      $tmparr[] = $_POST['fd_form']['company_type'][$i];
      $tmparr[] = $_POST['fd_form']['service_type'][$i];
      $tmparr[] = $_POST['fd_form']['service_name'][$i];
      $tmparr[] = $_POST['fd_form']['service_number'][$i];
      $tmparr[] = $_POST['fd_form']['start_datetime'][$i];
      $tmparr[] = $_POST['fd_form']['end_datetime'][$i];
      if (array_search('', $tmparr) === false) {
        // 値をセット
        $stmt->bindValue(":company_type{$i}", $_POST['fd_form']['company_type'][$i], PDO::PARAM_INT);
        $stmt->bindParam(":service_type{$i}", $_POST['fd_form']['service_type'][$i], PDO::PARAM_INT);
        $stmt->bindParam(":service_name{$i}", $_POST['fd_form']['service_name'][$i], PDO::PARAM_STR);
        $stmt->bindParam(":service_number{$i}", $_POST['fd_form']['service_number'][$i], PDO::PARAM_STR);
        $stmt->bindParam(":start_datetime{$i}", $_POST['fd_form']['start_datetime'][$i], PDO::PARAM_STR);
        $stmt->bindParam(":end_datetime{$i}", $_POST['fd_form']['end_datetime'][$i], PDO::PARAM_STR);
        $stmt->bindParam(":update_datetime{$i}", $current_date, PDO::PARAM_STR);
        $counter++;
      }
    }


    // SQLクエリの実行
    $res = $stmt->execute();
    // コミット
    $res = $pdo->commit();
  } catch (Exception $e) {
    // エラーが発生した時はロールバック
    $pdo->rollBack();
  }
  if ($res) {
    // 多重投稿防止
    $_SESSION['success_message'] = "{$counter}件登録が完了しました。";
    // プリペアドステートメントを削除
    $stmt = null;
    // 多重投稿防止
    header('Location: ?page=home');
  } else {
    $error_message = '登録に失敗しました。';
  }
}




//----------------------------------------------------------------------------------------
/*============


ページ情報保管
============*/
if (!empty($_GET['page'])) {
  $_SESSION['fd_console_page_state'] = 'fd_console_' . $_GET['page'];
} else {
  if (empty($_SESSION['fd_console_page_state'])) {
    $_SESSION['fd_console_page_state'] = 'fd_console_home';
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
  <title>Pcon FD管理コンソール</title>

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


  <!--===================================================
    FD管理コンソール表示
    ====================================================-->

  <?php if ($_SESSION['fd_console_page_state'] === 'fd_console_home') { ?>

    <a href="../fd-traffic/">トラフィックメニュー</a>
    <div class="fd_console_home_panel">
      <a href="./?page=edit" role="button" class="btn btn-secondary">追加</a>
      <div style="color: green;">
        <?php if (!empty($_SESSION['success_message'])) {
          echo $_SESSION['success_message'];
          unset($_SESSION['success_message']);
        } ?>
      </div>
    </div>
    <div id="fd_console_table"></div>

  <?php } ?>



  <?php if ($_SESSION['fd_console_page_state'] === 'fd_console_edit') { ?>




    <form id="fd_form" method="post" onsubmit="return fd_form_validation()">

      <div class="fd_console_home_panel">
        <a href="./?page=home" role="button" class="btn btn-secondary">戻る</a>
        <?php if (!empty($error_message)) { ?>
          <div style="color: red;"><?php echo $error_message; ?></div>
        <?php } ?>
      </div>


      <table id="fd_form_table" class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <td>取消</td>
            <td>大分類</td>
            <td>中分類</td>
            <td>サービス名称</td>
            <td>サービス番号</td>
            <td>開始日</td>
            <td>終了日</td>
          </tr>
        </thead>
        <tbody id="fd_form_tbody">
        </tbody>
        <tbody>
          <td style="border: 1px solid rgb(246,247,249);">
            <input type="button" id="fd_form_template_add" value="＋" class="btn btn-outline-secondary">
          </td>
          <td style="border: 1px solid rgb(246,247,249);">
            <input type="button" id="fd_form_template_add_plus5" value="＋5" class="btn btn-outline-secondary">
            <input type="button" id="fd_form_template_add_copy" value="copy+" class="btn btn-outline-secondary">
          </td>
          <td style="border: 1px solid rgb(246,247,249);">
            <input type="button" id="fd_form_template_add_copyplus5" value="copy+5" class="btn btn-outline-secondary">
          </td>
          <td colspan="4" style="border: 1px solid rgb(246,247,249);"></td>
        </tbody>
      </table>

      <div class="fd_console_home_panel">

        <input type="submit" name="fd_console_edit_submit" value="登録" class="form-control">
      </div>

    </form>

  <?php } ?>












  <!-----------------------------------------------------
  JavaScript読み込み
  ------------------------------------------------------>
  <script>
    let fd_console_company = <?php echo json_encode($fd_console_company); ?>;
    let fd_console_dial = <?php echo json_encode($fd_console_dial); ?>;
    let fd_console_base = <?php echo json_encode($fd_console_base); ?>;
    let fd_console_dialbase = <?php echo json_encode($fd_console_dialbase); ?>;
    const fd_console_page_state = "<?php echo $_SESSION['fd_console_page_state']; ?>";
    //console.log(fd_console_company)
  </script>
  <script src="../js/mybrary.js"></script>
  <script src="../_inc/config.js"></script>
  <script src="../js/script.js"></script>
</body>

</html>