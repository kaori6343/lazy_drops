<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

if(!empty($_POST)){
  debug('POST送信があります。');

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //未入力チェック
  validRequired($email, 'msg');
  validRequired($pass, 'msg');

  if(empty($err_msg)){
  //email形式チェック
  validEmail($email, 'msg');
  //emailの最大文字数チェック
  validMaxLen1($email, 'msg');

  //パスワードの半角文字数チェック
  validHalf($pass, 'msg');
  //パスワードの最大文字数チェック
  validMaxLen1($pass, 'msg');
  //パスワードの最小文字数チェック
  validMinLen($pass, 'msg');

  if(empty($err_msg)){
    debug('バリデーションokです。');
    try {

    $dbh = dbConnect();

    $sql = 'SELECT pass, id FROM users WHERE email = :email AND delete_flg = 0';

    $data = array(':email' => $email);

    $stmt = queryPost($dbh, $sql, $data);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    debug('クエリ結果の中身：'.print_r($result,true));
    //パスワード照合
    if( !empty($result) && password_verify($pass, $result['pass'])) {
      debug('パスワードがマッチしました。');

      //ログイン有効期限（デフォルトを１時間とする）
      $sesLimit = 60*60;
      //最終ログイン日時を現在日時に
      $_SESSION['login_date'] = time();

      //ログイン保持にチェックがある場合
      if($pass_save){
        debug('ログイン保持にチェックがあります。');
        //ログイン有効期限を30日にしてセット
        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
      }else{
        debug('ログイン保持にチェックはありません。');
        //次回からログイン保持しないので。ログイン有効期限を１時間後にセット
        $_SESSION['login_limit'] =  $sesLimit;
      }
      //ユーザーIDを格納
      $_SESSION['user_id'] = $result['id'];

      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('マイページへ遷移します。');
      header("Location:blog-mypage.php");

    }else{
      debug('パスワードがアンマッチです。');
      $err_msg['msg'] = MSG07;
    }
  }catch (Exception $e){
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['msg'] = MSG11;
  }

  }

}
}
debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>
<?php
$siteTitle = "Blog | 高校生バンド「Lazy Drop's」";
require('head.php');
?>

<body class="page-contact">

  <?php
  require('header.php');
  ?>

<div class="container">
  <section class="show-details">
    <div class="corner-title">
    <h2>BLOG</h2>
    </div>
    <div class="details">
      <!-- <div class="form-title">
      <h3>ログイン</h3>
      </div> -->
     <p class="err_msg"><?php if(!empty($err_msg['msg'])) echo $err_msg['msg']; ?></p>
     <form action="" method="post" class="login-area">
       <input type="text" name="email" placeholder="登録のメールアドレス" class="login-space">
       <input type="password" name="pass" placeholder="password" class="login-space">
       <label>
       <input type="checkbox" name="pass_save" class="pass_save">次回ログインを省略する
       </label>
       <button type="submit" name="submit" class="login-btn">ログイン</button>
     </form>
    </div>
    <div class="login-msg">
    <p>BLOGを見るには会員登録（無料）が必要です。登録は<a href="login.php">こちら</a>から。<br>
      <a href="passRemindSend.php">■ パスワードを忘れた場合 ■</a></p>
  </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
