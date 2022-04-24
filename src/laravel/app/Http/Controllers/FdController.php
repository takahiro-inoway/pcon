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

    /// Access-Control-Allow-Originエラーを回避する
    // header("Access-Control-Allow-Origin: *");

    //include("./config.blade.php"); // cnofigを展開。

    /*============
★SELECT


各種データ取得
============*/
    /*
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
*/
    //----------------------------------------------------------------------------------------
    /*============
★INSERT
fd_console_form_submitがなされていれば
fd_console_dialにレコード追加。
baseに入力があればfd_console_dialbaseにも追加する。
============*/
    /*
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

*/


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


    $values = 'test';
    return view('fd/console', compact('values'));
  }
  //
  public function traffic ()
  {
    $values = 'test';
    return view('fd/traffic', compact('values'));
  }
}
