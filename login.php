<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){
  debug('POST送信があります。');


  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $re_pass = $_POST['re_pass'];

  //未入力チェック
  validRequired($name, 'name');
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($re_pass, 're_pass');

  if(empty($err_msg)){

  //名前の最大文字数チェック
  validMaxLen($name, 'name');

  //Emailの形式チェック
  validEmail($email, 'email');
  //Emailの最大文字数チェック
  validMaxLen1($email, 'email');
  //Emailの重複チェック
  validEmailDup($email);

  //パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  //パスワードの最小文字数チェック
  validMinLen($pass, 'pass');
  //パスワードの最大文字数チェック
  validMaxLen1($pass, 'pass');

  if(empty($err_msg)){
  //パスワードとパスワード再入力が合っているか
  validMatch($pass, $re_pass, 're_pass');



  if(empty($err_msg)){
    debug('バリデーションok。');
    //例外処理
    try {
    $dbh = dbConnect();

    $sql = 'INSERT INTO users (username, email, pass, login_time, create_date) VALUES (:username, :email, :pass, :login_time, :create_date)';

    $data = array(':username' => $name, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));

    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    //クエリ成功の場合
    if($stmt){
      //ログイン有効期限（デフォルトを１時間とする）
      $sesLimit = 60*60;
      //最終ログイン有効期限（デフォルトを１時間とする）
      $_SESSION['login_date'] = time();
      $_SESSION['login_limit'] = $sesLimit;
      //ユーザーIDを格納
      $_SESSION['user_id'] = $dbh->lastInsertId();

      debug('セッション変数の中身：'.print_r($_SESSION,true));

      header("Location:blog-mypage.php");
    // }else{
    //   error_log('クエリに失敗しました。');
    //   $err_msg['common'] = MSG11;
    }

  } catch (Exception $e){
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG11;

  }
  }
}
}
}
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
    <h2>FAN CLUB</h2>
    </div>
    <div class="details">
      <div class="area-msg">
        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
      </div>
     <form action="" method="post" class="login-area">
          <p class="err_msg"><?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?></p>
       <input type="text" name="name" placeholder="名前" class="login-space">
          <p class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></p>
       <input type="text" name="email" placeholder="メールアドレス" class="login-space">
          <p class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></p>
       <input type="password" name="pass" placeholder="password(6文字以上の半角英数字)" class="login-space">
          <p class="err_msg"><?php if(!empty($err_msg['re_pass'])) echo $err_msg['re_pass']; ?></p>
       <input type="password" name="re_pass" placeholder="password(再入力)" class="login-space">
       <button type="submit" name="submit" class="login-btn">無料登録</button>
     </form>
    </div>
    <div class="login-msg">
    <p>会員の方は<a href="blog.php">こちら</a>からログインしてください。</p>
  </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
