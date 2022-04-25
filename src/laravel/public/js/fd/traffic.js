"use strict"
// DOM形成後要素を取得する。
window.addEventListener('DOMContentLoaded', function () {
  // ロード画面を閉じる
  document.getElementById("loading_window").style.display = "none";
});

//
// 処理モジュール
const Module = new class {
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
//
// コントローラー
const Controllist = new class {
  traffic() {
    // テーブルのカラム名を変換する。
    const colname_changer = (colname, reverse = 'none') => {
      const tmp = {
        target_datetime: '照会区間',
        service_number: 'サービス番号',
        dial_place: '都道府県名',
        all_calls: '総呼数',
        completion: '完了呼数',
        wait_completion: '待合後完了呼',
        completion_rate: '接続完了率',
        not_completion: '不完了呼',
        overtime: '時間外呼',
        encount: '話中遭遇呼',
        fd_busy: 'ＦＤ話中',
        ls_busy: '着ＬＳ話中',
        halfway_giveup: '途中放棄',
        calling_giveup: '呼中放棄',
        unrespons_encount: '無応答遭遇呼',
        talking_giveup: '案内中放棄',
        connection_denied: '接続拒否呼',
        outside_region: '地域外呼',
        rimitover: '限度超過',
        spam_filter: '迷惑拒否',
        wait_giveup: '待中放棄',
        wait_timeover: '待時超過',
        wait_rimitover: '待数超過',
        wait_retry: '待リトライオーバー',
        message_store: 'メッセージ蓄積呼数',
        other: 'その他',
        average_talktime: '平均通話時間',
        waittime_finish: '待後完了平均待合時間',
        waittime_giveup: '待中放棄平均待合時間',
      }
      if (reverse === 'reverse') {
        const tmp2 = Object.fromEntries(
          Object.entries(tmp).map(function (value) {
            return [value[1], value[0]];
          })
        );
        return tmp2[colname]
      } else {
        return tmp[colname];
      }
    }



    let tmpstr = ''; // HTML用文字列を作成。
    Modelist.DB('dials').forEach(dial => {
      tmpstr += `
      <option value="${dial['service_number']}">
      ${dial['service_number']}：${dial['service_name']}
      </option>
      `
    })
    document.getElementById('traffic_service_number').innerHTML = tmpstr;

    // フォーム入力の初期値を代入。
    try {
      document.getElementById('traffic_start_date').value = post_param['traffic_start_datetime'][0];
      document.getElementById('traffic_start_time').value = post_param['traffic_start_datetime'][1];
      document.getElementById('traffic_end_date').value = post_param['traffic_end_datetime'][0];
      document.getElementById('traffic_end_time').value = post_param['traffic_end_datetime'][1];
      document.getElementById('traffic_service_number').value = post_param['traffic_service_number'];
      document.getElementById('traffic_dial_place').value = post_param['traffic_dial_place'];
    } catch {
      const today = new Date();
      const tmp = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
      const tmp2 = ('0' + today.getHours()).slice(-2) + ':00';
      document.getElementById('traffic_start_date').value = tmp;
      document.getElementById('traffic_start_time').value = tmp2;
      document.getElementById('traffic_end_date').value = tmp;
      document.getElementById('traffic_end_time').value = tmp2;
    }

    // CSV出力を行う。
    const csv_export = (e) => {
      // 出力用配列。
      const tmpcsv = [];
      // ラベルを取得する。
      const collist = Object.keys(Modelist.DB('originvalues')[0]);
      // ラベルの格納。
      tmpcsv.push(collist);
      // データの格納。
      Modelist.DB('originvalues').forEach(traffic => {
        tmpcsv.push(collist.map(col => {
          return (traffic[col]);
        }));
      });
      Module.CsvExportDownload(tmpcsv, '_番号別発信地域別時間別', e.target.dataset.encoding, true);
    }
    document.getElementById('traffic_csv_export_sjis').addEventListener('click', csv_export);
    document.getElementById('traffic_csv_export_utf8').addEventListener('click', csv_export);


    // jsGridでの表示のため配列を返す。
    const fieldlist = () => {
      return Object.keys(Modelist.DB('originvalues')[0]).map(key => {
        if (key === 'target_datetime' || key === 'dial_place' || key === 'average_talktime') {
          return { name: key, type: 'text' };
        } else {
          return { name: key, type: 'number' };
        }
      });
    }
    // jsGridでの表示
    const clients = Modelist.DB('originvalues');
    $("#jsGrid").jsGrid({
      width: "100%",
      height: "400px",
      sorting: true,
      paging: true,
      selecting: true,
      autoload: true,
      pageSize: 100,
      /*
      filtering: true,
      inserting: true,
      editing: true,
      pageButtonCount: 5,
      controller: db,
      pagerContainer: "#externalPager",
      pagerFormat: "current page: {pageIndex} &nbsp;&nbsp; {first} {prev} {pages} {next} {last} &nbsp;&nbsp; total pages: {pageCount}",
      pagePrevText: "<",
      pageNextText: ">",
      pageFirstText: "<<",
      pageLastText: ">>",
      pageNavigatorNextText: "&#8230;",
      pageNavigatorPrevText: "&#8230;",
      */

      data: clients,
      fields: fieldlist(),
    });

  }
}
//
// ビューの制御
const Viewist = new class {
  validation() {
    if (document.getElementById('traffic_start_date').value === '') {
      alert('開始日を指定してください。')
      return false
    }
    if (document.getElementById('traffic_end_date').value === '') {
      alert('終了日を指定してください。')
      return false
    }
    return true
  }

  initialize() {
    // デートピッカーを IDに付与。
    flatpickr("#traffic_start_date", {
      locale: "ja"
    });

    flatpickr("#traffic_end_date", {
      locale: "ja"
    });
    //タイムピッカーをクラスに付与。
    $('.timepicker').timepicker({ 'step': 60 });
  }

}
//
// ルーティング
const Routing = new class {
  request() {
    Controllist.traffic();
  }
}
Routing.request();
