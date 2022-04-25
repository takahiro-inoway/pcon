



/*=====================================================
DOM生成後処理
=====================================================*/
// DOM形成後要素を取得する。
window.addEventListener('DOMContentLoaded', function () {
  // ロード画面を閉じる
  document.getElementById("loading_window").style.display = "none";
});

// Mybrary.js からクラスを取得
const Myb = new Mybrary();



/*================================================================




                        【fd_console】




================================================================*/

/*=============================================


FD管理画面の表示。
fd_console/ のみ表示処理。
=============================================*/


/*================


HTML用パーツ。
================*/
// 大分類の option
const fd_form_company_typelist = `
<option value="0">PFI</option>
<option value="1">FL</option>
<option value="2">明治薬品</option>
`;
// 中分類の option
const fd_form_service_typelist = `
<option value="0">新規</option>
<option value="1">既存</option>
`;


/*================


画面表示用関数
================*/
/**
 * @method company_type_changer | 大分類の 数値➡文字列、文字列➡数値 を行う。
 * @method service_type_changer | 中分類の 数値➡文字列、文字列➡数値 を行う。
 * @method ymd_format | 日付表示を 'Y-m-d'型もしくは 'Y-m-d h:i'に変換して返す
 */
const Methods = new class {
  /**
   * company_typeの数値に対応する文字列を返す。
   * [0=PFI,1=FL,2=明治薬品,default=未定義]
   * 第二引数に "reverse"を指定で文字列から数値を返す。
   */
  company_type_changer = (num, reverse = 'none') => {
    let array = {
      0: 'PFI',
      1: 'FL',
      2: '明治薬品'
    }
    if (reverse === 'reverse') {
      array = {
        PFI: '0',
        FL: '1',
        明治薬品: '2'
      }
    }
    return array[num];
  }
  /**
   * service_typeの数値に対応する文字列を返す。
   * [0=新規,1=既存,default=未定義]
   * 第二引数に "reverse"を指定で文字列から数値を返す。
   */
  service_type_changer = (num, reverse = 'none') => {
    let array = {
      0: '新規',
      1: '既存'
    }
    if (reverse === 'reverse') {
      array = {
        新規: '0',
        既存: '1'
      }
    }
    return array[num]
  }

  /**
   * 日付表示を 'Y-m-d'型に変換して返す。
   * 時間表示が不要な時などに使用する。
   * 第二引数に "ymdhi"を指定で時分まで返す。
   */
  ymd_format = (datetime, format = 'ymd') => {
    let tmp = new Date(datetime);
    let tmpstr = '';
    if (format === 'ymd') {
      tmpstr = `${tmp.getFullYear()}-${('0' + tmp.getMonth() + 1).slice(-2)}-${('0' + tmp.getDate()).slice(-2)}`;
    } else if (format === 'ymdhi') {
      tmpstr = `${tmp.getFullYear()}-${('0' + tmp.getMonth() + 1).slice(-2)}-${('0' + tmp.getDate()).slice(-2)} ${tmp.getHours()}:${('0' + tmp.getMinutes()).slice(-2)}`;
    }
    if (tmpstr.indexOf('N') !== -1) {
      tmpstr = '---';
    }
    return tmpstr;
  }
  /**
   * ラベル表示を DBのカラム名に変換して返す。
   * 
   * 第二引数に "reverse"を指定で逆判定可能。
   */
  label_dbcolname = (str, reverse = 'none') => {
    let array = {
      大分類: 'company_type',
      中分類: 'service_type',
      サービス名称: 'service_name',
      サービス番号: 'service_number',
      開始日: 'start_datetime',
      終了日: 'end_datetime',
      更新日: 'update_datetime',
      登録日: 'datetime',
      ID: 'id'
    }
    if (reverse === 'reverse') {
      array = {
        company_type: '大分類',
        service_type: '中分類',
        service_name: 'サービス名称',
        service_number: 'サービス番号',
        start_datetime: '開始日',
        end_datetime: '終了日',
        update_datetime: '更新日',
        datetime: '登録日',
        id: 'ID'
      }
    }
    return array[str];
  }
}


/*================


Ajax用関数
================*/
const Ajax = new class {
  /**
   * @param postdata | [['パラメータ名','値'],['パラメータ名','値']...]の形で渡す。
   */
  fd_console_update = (postdata) => {
    let request = new XMLHttpRequest();
    // 送信する値を fd に append する。PHP側で $_POST['id'] とかで受け取れる。
    let fd = new FormData();
    postdata.forEach(post => fd.append(post[0], post[1]));

    // リクエストを open する。(?)
    request.open('POST', AJAXPHP_FILEPATH, true);
    // ロードしてみていけたらOK出してると思う。たぶん。
    request.onerror = function (event) {
      console.log(event.type); // => "error"
    };
    // 値含めて送信。
    request.send(fd);
    request.onreadystatechange = function (event) {
      if (request.readyState === 4 && request.status === 200) {
        try {
          // post送信が成功したら次の関数の引数でデータを渡す。
          console.log(request.responseText); // echoされたもの
          console.log(request.statusText); // => "OK"
        } catch {

        }
      } else {
        console.log(request.statusText); // => Error Message
      }
    }
  }
}


/*================
【テーブル画面】

fd_console_homeのみ処理
・テーブル表示
================*/
const Fd_Console_Home_State = () => {

  /*================  
   Grid.jsでテーブルを表示。
  ================*/
  // thead用配列。
  let fd_console_output_head = [
    '大分類',
    '中分類',
    'サービス名称',
    'サービス番号',
    '開始日',
    '終了日',
    '更新日',
    '登録日',
    'ID'
  ];
  // tbody用配列。
  let fd_console_output_body = [];
  let tmp = [];
  fd_console_dial.forEach(dial => {
    tmp = [];
    tmp = [
      Methods.company_type_changer(dial['company_type']),
      Methods.service_type_changer(dial['service_type']),
      dial['service_name'],
      dial['service_number'],
      Methods.ymd_format(dial['start_datetime']),
      Methods.ymd_format(dial['end_datetime']),
      Methods.ymd_format(dial['update_datetime'], 'ymdhi'),
      Methods.ymd_format(dial['datetime'], 'ymdhi'),
      dial['id']
    ];
    fd_console_output_body.push(tmp);
  })

  // Grid.jsで表示。
  const grid = new gridjs.Grid({
    pagination: {
      limit: 100
    },
    search: true,
    sort: true,
    fixedHeader: true,
    height: '80vh',
    columns: fd_console_output_head, // thead
    data: fd_console_output_body,    // tbody
    className: {
      td: 'fd_console_td',
      table: 'fd_console_table'
    },
    language: {
      'search': {
        'placeholder': '文字入力で絞込み'
      },
      'pagination': {
        'previous': '◀',
        'next': '▶',
        'showing': '100件ずつ表示しています。',
        'results': () => '件ヒットしました。'
      }
    },
    style: {
      th: {
        'padding': '2px 8px',
        'margin': '0',
      },
      td: {
        'padding': '2px 8px'
      },
      footer: {
        'padding': '2px 8px'
      }
    }
  }).render(document.getElementById("fd_console_table"));

  /*================  
   イベント系の処理。
  ================*/
  // フォーム表示イベントを作成。
  const fd_console_formchange = (e) => {

    let tmp = e.target.innerText;
    e.target.innerText = '';
    // 要素を作成：取得したタイプによって作成する要素を分ける。
    let ele;
    switch (e.target.dataset.columnId) {
      case '大分類': // SELECT
        ele = document.createElement('select');
        ele.innerHTML = fd_form_company_typelist; // パーツを使用する。
        ele.selectedIndex = Methods.company_type_changer(tmp, 'reverse'); // 表示されていたものを選択状態にする。
        break;
      case '中分類': // SELECT
        ele = document.createElement('select');
        ele.innerHTML = fd_form_service_typelist; // パーツを使用する。
        ele.selectedIndex = Methods.service_type_changer(tmp, 'reverse'); // 表示されていたものを選択状態にする。
        break;
      default:      // INPUT
        ele = document.createElement('input');
        ele.setAttribute('type', 'text');
        ele.setAttribute('value', tmp);
    }
    // 要素にイベント付与：フォーカスが外れたらフォームを消して親のイベントを削除する。
    ele.addEventListener('blur', (e) => {
      console.log(e.target.tagName);
      let val;   // Ajaxで送信する実際の変更値。
      let label = ''; // SELECTの場合のラベル取得。
      if (e.target.tagName !== 'SELECT') {
        val = e.target.value;   // 送信する値を取得。
        label = e.target.value; // ラベルはないので同じ値。
      } else {
        val = e.target.value; // 送信する値を取得。
        label = e.target.options[e.target.value].text; // optionのラベルを取得。
      }
      // ajax送信で DBの値を更新する。
      console.log(e.target)
      Ajax.fd_console_update([
        ['fd_console_update', 'fd_console_update'],
        ['id', e['path'][2].lastElementChild.innerText],
        [Methods.label_dbcolname(e.target.parentElement.dataset.columnId), val],
        ['column', Methods.label_dbcolname(e.target.parentElement.dataset.columnId)]
      ]);
      // dblclickでフォーム送信のイベント削除。
      e.target.parentElement.removeEventListener('dblclick', fd_console_formchange);
      e.target.parentElement.innerHTML = label; // ラベルをHTMLに反映。文字だけにする。
    })

    e.target.appendChild(ele); // HTMLに反映させる。
    ele.focus();  // フォーカス状態にする。
  }

  // grid.jsのイベントメソッドでイベントを付与。
  grid.on('cellClick', (...e) => {

    // 押下状態のクラスを全削除。
    tmparr = Array.from(document.getElementsByClassName('fd_console_td_enter'));
    tmparr.forEach(tmp => tmp.classList.remove('fd_console_td_enter'));
    // 押下状態のクラスを付与。
    Array.from(e[0]['path'][1].children).forEach(childs => childs.classList.add('fd_console_td_enter'));

    tmparr = Array.from(document.getElementsByClassName('fd_console_td_enter'));
    // 押下した要素に dblclickで入力フォーム表示イベントを付与。
    const notarray = ['更新日', '登録日', 'id']; // この配列の要素にはイベント付与しない。
    tmparr.forEach(tmp => {
      if (notarray.includes(tmp.dataset.columnId) === false)
        tmp.addEventListener('dblclick', fd_console_formchange)
    });

  })

}




/*================
【編集画面】

【fd_console_edit】のみ処理。
・レコード追加フォームの表示。
================*/
const Fd_Console_Edit_State = () => {

  // fd_form_tbodyの name属性用配列
  const fd_form_namelist = [
    'fd_form[company_type][]',
    'fd_form[service_type][]',
    'fd_form[service_name][]',
    'fd_form[service_number][]',
    'fd_form[start_datetime][]',
    'fd_form[end_datetime][]'
  ];

  // fd_form_tbody用のテンプレート文字列、大分類と中分類はパーツを使用する。
  const fd_form_template = `
  <tr>
    <td><input type="button" value="✕" class="btn form-control fd_form_template_delete"></td>
    <td>
      <select class="form-control" name="${fd_form_namelist[0]}" id="fd_form_company_type">
      ${fd_form_company_typelist}
      </select>
    </td>
    <td>
      <select class="form-control" name="${fd_form_namelist[1]}" id="fd_form_service_type">
      ${fd_form_service_typelist}
      </select>
    </td>
    <td><input type="text" name="${fd_form_namelist[2]}" id="fd_form_service_name" class="form-control"></td>
    <td><input type="number" name="${fd_form_namelist[3]}" id="fd_form_service_number" class="form-control"></td>
    <td><input type="text" name="${fd_form_namelist[4]}" id="fd_form_start_datetime" class="form-control"></td>
    <td><input type="text" name="${fd_form_namelist[5]}" id="fd_form_end_datetime" class="form-control"></td>
  </tr>
  `;

  // fd_form_tbody用の文字列反映、ライブラリ適用、イベント付与。
  const fd_form_tbady_output = (copy = 'none') => {
    // HTMLを挿入。
    document.getElementById('fd_form_tbody').insertAdjacentHTML('beforeend', fd_form_template);
    // ピッカーを適用。
    flatpickr("#fd_form_start_datetime", {
      locale: "ja"
    });
    flatpickr("#fd_form_end_datetime", {
      locale: "ja"
    });
    // ✕ボタンイベントを付与。
    let tmp = Array.from(document.getElementsByClassName('fd_form_template_delete'));
    for (let i = 0; i < tmp.length; i++) {
      tmp[i].addEventListener('click', (e) => {
        e.target.parentElement.parentElement.remove();
      })
    }
    // 引数(copy='copy')の場合コピー処理。fd_form_tbody用テンプレート用配列を使用する。
    if (copy === 'copy') {
      fd_form_namelist.forEach(name => {
        tmp = document.getElementsByName(name);
        try {
          tmp[tmp.length - 1].value = tmp[tmp.length - 2].value;
        } catch {
        }
      })
    }
  }
  // 初期状態で１つ表示しておく。
  fd_form_tbady_output();
  // ＋ボタンにフォーム要素追加イベントを付与。
  document.getElementById('fd_form_template_add').addEventListener('click', () => {
    fd_form_tbady_output();
  })
  document.getElementById('fd_form_template_add_plus5').addEventListener('click', () => {
    for (let i = 0; i < 5; i++) {
      fd_form_tbady_output();
    }
  })
  document.getElementById('fd_form_template_add_copy').addEventListener('click', () => {
    fd_form_tbady_output('copy');
  })
  document.getElementById('fd_form_template_add_copyplus5').addEventListener('click', () => {
    for (let i = 0; i < 5; i++) {
      fd_form_tbady_output('copy');
    }
  })
}


/*================
ページを判定して
表示用関数を呼び出す。
================*/
try {
  switch (fd_console_page_state) {
    case 'fd_console_home':
      Fd_Console_Home_State();
      break;
    case 'fd_console_edit':
      Fd_Console_Edit_State();
      break;
    default:
  }
} catch {

}





/*================


バリデーション
================*/

// fd_form のバリデーション 大分類、サービス名称、サービス名のみ判定。
const fd_form_validation = () => {
  return true;
  let tmparr = [
    'fd_form_company_type',
    'fd_form_service_type',
    'fd_form_service_name',
    'fd_form_service_number',
    'fd_form_start_datetime',
    'fd_form_end_datetime'
  ];
  for (let i = 0; i < tmparr.length; i++) {
    if (document.getElementById(tmparr[i]).value === '') {
      console.log(tmparr[i]);
      console.log(document.getElementById(tmparr[i]).value);
      alert("未入力項目が存在します。");
      return false;
    }
  }
  return true;
}












/*================================================================




                        【fd_】




================================================================*/


//===Ajax===========================================================
// 引数：post_name::post送信する値のキー名称。
//      post_data::post送信する値のキー名称に対応する値。
// ここでは値の送信なのかインターバル取得なのかは全く判定しない。
// ただ引数にとった値をpost送信するだけ。判定はphp側で行う。
const AjaxSend = (post_name, post_data) => {
  let request = new XMLHttpRequest();
  // 送信する値を fd に append する。PHP側で $_POST['id'] とかで受け取れる。
  let fd = new FormData();
  fd.append(post_name, post_data);
  // リクエストを open する。(?)
  request.open('POST', AJAXPHP_FILEPATH, true);
  // ロードしてみていけたらOK出してると思う。たぶん。
  request.onerror = function (event) {
    console.log(event.type); // => "error"
  };
  // 値含めて送信。
  request.send(fd);
  request.onreadystatechange = function (event) {
    if (request.readyState === 4 && request.status === 200) {
      try {
        // post送信が成功したら次の関数の引数でデータを渡す。
        Post_Message(JSON.parse(request.responseText));
        console.log(request.statusText); // => "OK"
      } catch {

      }
    } else {
      console.log(request.statusText); // => Error Message
    }
  }
}
// post送信が完了した際に呼び出される関数。
// 引数がDBデータとなり、html内の要素に代入する処理を記述する。
const Post_Message = (json) => {
  let tmp = '';
  for (let i = 0; i < json.length; i++) {
    tmp += '<p>' + json[i]['message'] + '</p>';
  }
  document.getElementById('message_view').innerHTML = tmp;
}
// フォームのクリックで発火し特定の値をpost送信にのせてDB登録する。
// インターバルの取得POSTか、値送信のPOSTかは引数の名称で判断する。
// $POST_['message']が!emptyなら値送信、$POST_['interval']が!emptyならインターバル、など。
const click_post = () => {
  AjaxSend('message', document.getElementById('text_message').value);
  // 値を削除
  document.getElementById('text_message').value = '';
}
// DBからinterval1で取得する。
const interval_post = () => {
  AjaxSend('interval', 'value'); // 渡す引数の値　value　は特に意味はない、何でもよい。
}
// setInterval(interval_post, 200);
//==================================================================






