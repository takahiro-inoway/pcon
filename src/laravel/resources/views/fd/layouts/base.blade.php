<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <link rel="icon" type="image/png" href="">
  <title>@yield('title')</title>
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

  @yield('content')

</body>

</html>