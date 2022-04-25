<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <link rel="icon" type="image/png" href="">
  <title>Pcon トラフィックメニュー</title>
  @include('fd/head')
  <link rel="stylesheet" href="{{ asset('/css/fd/style.css')}}">
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

  <a href="{{ asset('fd/console') }}">FD管理コンソール</a>


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
  <!-----------------------------------------------------
  JavaScript読み込み
  ------------------------------------------------------>
  <script>
    const DB_RECORDS = @json($traffics);
    console.log(@json($traffics))
    console.log(DB_RECORDS.post_param);
  </script>
  <script src="{{ asset('/js/fd/mybrary.js') }}"></script>
  <script src="{{ asset('/js/fd/config.js') }}"></script>
  <script src="{{ asset('/js/fd/traffic.js') }}"></script>
</body>

</html>