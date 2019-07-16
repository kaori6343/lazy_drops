<?php
//================================
// ログ
//================================
ini_set('log_errors','on');
ini_set('error_log','php.log');
//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
  error_log('デバッグ:'.$str);
}
}
//================================
// セッション準備・セッション有効期限を延ばす
//================================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();

//================================
//画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// 定数
//================================
define('MSG01', '入力必須です。');
define('MSG02', 'Emailの形式で入力してください。');
define('MSG03', '256文字以内で入力してください。');
define('MSG04', '200文字以内で入力してください。');
define('MSG05', '6文字以上で入力してください。');
define('MSG06', '半角英数字のみご利用ください');
define('MSG07', 'メールアドレスまたはパスワードが違います');
define('MSG08', 'パスワード（再入力）が合っていません。');
define('MSG09', 'そのEmailは既に登録されています。');
define('MSG10', 'そのEmailは既に登録されています。');
define('MSG11', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG12', '20文字以内で入力してください。');
define('MSG13', '古いパスワードが違います。');
define('MSG14', '古いパスワードと同じです。');
define('MSG15', '文字で入力してください。');
define('MSG16', '正しくありません。');
define('MSG17', '有効期限が切れています。');
define('SUC01', 'お問い合わせを受け付けました。');
define('SUC02', 'パスワードを変更しました。');
define('SUC03', '登録情報を変更しました。');
define('SUC04', 'メールを送信しました。');
define('SUC05', '画像を保存しました。');



//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（emailの形式チェック）
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（名前の最大文字数チェック）
function validMaxLen($str, $key, $max = 20){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen1($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（textarea最大文字数チェック）
function validMaxLen2($str, $key, $max = 200){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min ){
  global $err_msg;
  $err_msg[$key] = MSG05;
}
}
//バリデーション関数（半角英数字チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG08;
  }
}
//バリデーション関数（Emailの重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try {
    //DBヘ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG10;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG11;
  }
}
//パスワードチェック
function validPass($str, $key){
  validHalf($str, $key);
  validMaxLen1($str, $key);
  validMinLen($str, $key);
}
//固定長チェック
function validLength($str, $key, $len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len . MSG15;
  }
}
//================================
//データベース
//================================
//DB関数
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=band;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時に例外をスロー
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //バッファードクエリを使う（一度に結果セットをすべて取得し、サーバー負荷を軽減）
    //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;  //??
}
function queryPost($dbh, $sql, $data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
  debug('クエリに失敗しました。');
  debug('失敗したSQL文：'.print_r($stmt,true));
  $err_msg['common'] = MSG11;
  return 0;
  }
  debug('クエリに成功しました。');
  return $stmt;
}
function getUser($u_id){
  debug('ユーザー情報を取得します。');
  //例外処理
  try{
    $dbh = dbConnect();

    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';

    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);



    if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }else {
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生：' .$e->getMessage());
  }
}
function getImgList(){
  debug('画像情報を取得します。');

  try {
    $dbh = dbConnect();

    $sql = 'SELECT pic FROM images';

    $data = array();

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      debug('SQL文：'.$sql);
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch(Exception $e) {
    error_log('エラー発生：' .$e->getMessage());
  }
}
//================================
//メール送信
//================================
function sendMail($from, $to, $subject, $comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    //文字化けしないように設定
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    //メール送信（送信結果はtrueかfalseで帰ってくる）
    $result = mb_send_mail($to, $subject, $comment, "From:".$from);

    if($result){
      unset($_POST);
      global $err_msg;
      $err_msg['name'] = SUC01;
    }else{
      global $err_msg;
      $err_msg['name'] = '送信できませんでした。';

}
}
}
//================================
//その他
//================================
//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
//フォーム入力保持
function getFormData($str){
  global $dbFormData;
  global $err_msg;
  //ユーザーデータがある場合
  if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      //POSTにデータがある場合
      if(isset($_POST[$str])){
        return sanitize($_POST[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }else{
      if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
        return sanitize($_POST[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($_POST[[$str]])){
      return sanitize($_POST[$str]);
    }
  }
}
    //認証キー生成
    function makeRandKey($length = 8) {
      $chars = 'abcdefghijklmnopqlstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $str = '';
      for ($i = 0; $i < $length; ++$i){
        $str .= $chars[mt_rand(0, 61)];
      }
      return $str;

      }

//画像処理
function uploadImg($file, $key){
      debug('画像アップロード処理開始');
      debug('FILE情報：'.print_r($file, true));

if(isset($file['error']) && is_int($file['error'])) {
        try{

          switch ($file['error']) {
            case UPLOAD_ERR_OK:
              break;
            case UPLOAD_ERR_NO_FILE:
            //ファイル未選択の場合
            throw new RuntimeException('ファイルが選択されていません。');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('ファイルが大きすぎます。');
            default:
              throw new RuntimeException('その他のエラーが発生しました。');
          }
          $type = @exif_imagetype($file['tmp_name']);
          if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
            throw new RuntimeException('画像形式が未対応です。');
          }
          $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
          if(!move_uploaded_file($file['tmp_name'], $path)){
            throw new RuntimeException('ファイル保存時にエラーが発生しました。');
          }
          chmod($path, 0644);
          debug('ファイルは正常にアップロードされました。');
          debug('ファイルパス：'.$path);
          return $path;
        } catch (RuntimeException $e) {
          debug($e->getMessage());
          global $err_msg;
          $err_msg[$key] = $e->getMessage();
        }
      }
    }
?>
