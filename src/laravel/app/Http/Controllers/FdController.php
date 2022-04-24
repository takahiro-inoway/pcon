<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Consolebase;
use App\Models\Consolecompany;
use App\Models\Consoledial;
use App\Models\Consoledialbase;
use App\Models\Originvalue;
use Illuminate\Support\Arr;

class FdController extends Controller
{
  //
  public function index()
  {
    $values = 'test';
    return view('fd/index', compact('values'));
  }
  //
  public function console()
  {

    $companys = Consolecompany::all();

    $values = 'test';
    return view('fd/console', compact('values'));

    // -----

    /// Access-Control-Allow-Originエラーを回避する
    // header("Access-Control-Allow-Origin: *");

    //include("./config.blade.php"); // cnofigを展開。

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

  }
  //
  public function traffic()
  {

    $traffics = array(
      'dials' => Consoledial::all()->sortByDesc('id'),
      'originvalues' => Originvalue::all()->sortByDesc('id'),
      'post_param' => $_POST
    );

    $values = 'test';
    return view('fd/traffic', compact('traffics'));


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

  }
}
