@if ($target == 'store')
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
@endif