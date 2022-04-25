// ajax処理用の phpファイルのパスを指定しておく。
//const AJAXPHP_FILEPATH = 'http://192.168.100.117/phplearn/Pcon/ajax.php';
const AJAXPHP_FILEPATH = 'http://localhost/phplearn/Pcon/ajax.php';

//
// ・モデルクラス、DBデータを返す。
//
const Modelist = (() => {
  // symbolを使用してModel.nameでアクセスできないようにする。
  const name = Symbol('name');
  return new class {
    constructor() {
      this[name] = DB_RECORDS;
    }
    DB(TableName) {
      if (!this[name][TableName]) return [];
      return this[name][TableName];
    }
  }
})();
