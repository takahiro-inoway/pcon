/*=====================================================

グローバル関数
=====================================================*/
/**
 * ClassAddevent => 指定したクラスをもつ要素全てに指定したイベントを付与する。
 * SelectCopy    => このイベントを付与された要素はクリックするとコピーされる。
 * ScrollMostBottom => 指定した要素の最下部までスクロールする。
 */
const Mybrary = class {
  // =======================================================================
  /**指定したクラスをもつ要素全てに指定したイベントを付与する。
   * @param {string}event_handler 発火するイベント
   * @param {string}class_name クラス名。
   * @param {function}function_name 付与するイベント。
   */
  ClassAddevent(event_handler, class_name, function_name) {
    let tmp = Array.from(document.getElementsByClassName(class_name));
    for (let i = 0; i < tmp.length; i++) {
      tmp[i].addEventListener(event_handler, function_name);
    }
  }
  // =======================================================================
  /**選択した要素の value を select状態にし、クリップボードにコピーする。 
   * @param {element}ele | 対象の要素。
   */
  SelectCopy(ele) {
    ele.target.select();
    let tmp = ele.target.value;
    navigator.clipboard.writeText(tmp).then(ele => {
    });
  }
  // =======================================================================
  /**"overfolow-y:scroll"をもつ要素を最下部までスクロールする。
   * @param {element}target_element | スクロールする要素。 
   */
  ScrollMostBottom(target_element) {
    target_element.scrollTop = target_element.scrollHeight;
  }
  // =======================================================================
  /**要素とクラス名を指定して inputを全て value="" にする。
   * @param {element}target_element | 指定する要素 
   * @param {string}target_class | 指定するクラス。
   */
  InputAllClear(target_element, target_class) {
    let tmp = target_element.getElementsByClassName(target_class);
    for (let i = 0; i < tmp.length; i++) {
      tmp[i].value = "";
    }
  }
  // =======================================================================
  /**二次元配列を渡すとCSVにしてダウンロードする。
   * ★encoding-japanese ライブラリを使用します！！
   * CDN : <script src="https://cdnjs.cloudflare.com/ajax/libs/encoding-japanese/1.0.30/encoding.min.js"></script>
   * @param {array}data array | CSVにする二次元配列。 
   * @param {string}filename 出力するファイル名称。省略可。
   * @param {string}encoding 省略すると "UTF8"、"SJIS"を渡すと変換して出力。
   * @param {bool}datetime trueでファイル名に日時付きで出力。省略可。
   */
  CsvExportDownload(data, filename = 'noname', encoding = 'UTF8', datetime = false) {
    //作った二次元配列をCSV文字列に直す。
    let csv_string = "";
    data.forEach(d => {
      csv_string += d.join(",");
      csv_string += '\r\n';
    })
    // エンコードが SJISの場合変換する。
    if (encoding === 'SJIS') {
      // encoding-japanese ライブラリで UTF-8=>SJISへ変換。
      csv_string = Encoding.stringToCode(csv_string);
      csv_string = Encoding.convert(csv_string, 'sjis', 'unicode');
      csv_string = new Uint8Array(csv_string);
    }
    // ファイル名作成
    if (datetime === true) {  // datetimeが trueなら日時を付与する。
      const date = new Date();
      filename += '_' + date.getFullYear()
        + ('0' + (date.getMonth() + 1)).slice(-2)
        + ('0' + date.getDate()).slice(-2)
        + ('0' + date.getHours()).slice(-2)
        + ('0' + date.getMinutes()).slice(-2)
        + ('0' + date.getSeconds()).slice(-2);
    }
    filename += '.csv';
    //CSVのバイナリデータを作る
    let blob = new Blob([csv_string], { type: "filename/csv" });
    let uri = URL.createObjectURL(blob);
    //リンクタグを作る
    let link = document.createElement("a");
    link.download = filename;
    link.href = uri;
    //作ったリンクタグをクリックさせる
    document.body.appendChild(link);
    link.click();
    //クリックしたら即リンクタグを消す
    document.body.removeChild(link);
    link = null;
  }
}


