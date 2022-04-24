<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <link rel="icon" type="image/png" href="">
  <title>Pcon FD管理コンソール</title>

  @include('fd/head')

  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
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
    /*
    let fd_console_company = <?php // echo json_encode($fd_console_company); 
                              ?>;
    let fd_console_dial = <?php // echo json_encode($fd_console_dial); 
                          ?>;
    let fd_console_base = <?php // echo json_encode($fd_console_base); 
                          ?>;
    let fd_console_dialbase = <?php // echo json_encode($fd_console_dialbase); 
                              ?>;
    const fd_console_page_state = "<?php // echo $_SESSION['fd_console_page_state']; 
                                    ?>";
    //console.log(fd_console_company)
    */
  </script>
  <script src="{{ asset('/js/mybrary.js') }}"></script>
  <script src="{{ asset('/js/config.js') }}"></script>
  <script src="{{ asset('/js/script.js') }}"></script>
</body>

</html>