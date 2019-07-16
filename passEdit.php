<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード編集ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報；'.print_r($userData,true));



if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST送信：'.print_r($_POST, true));

  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //未入力チェックを行う
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(empty($err_msg)){

    //新しいパスワードのチェック
    validPass($pass_new, 'pass_new');
    //古いパスワードとDBパスワード照合
    if(!password_verify($pass_old, $userData['pass'])){
      $err_msg['pass_old'] = MSG13;

    }
    //古いパスワードと新しいパスワードが同じかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG14;
    }
    //パスワードとパスワード再入力が合っているかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
      debug('バリデーションok');

      try{
        $dbh = dbConnect();

        $sql = 'UPDATE users SET pass = :pass WHERE id = :id';

        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));

        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        // if($stmt){
        //   $success_msg = SUC02;

          //メールを送信
          $username = ($userData['username']) ? $userData['username'] : '名無し';
          $from = 'info@lazydrops.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知｜Lazy Drops';
          $comment = <<<EOT
{$username} さん
パスワードが変更されました。

////////////////////////////////////////
Lazy Drop's staff
URL  http://webukatu.com/
E-mail info@lazydrops.com
////////////////////////////////////////
EOT;
         // sendMail($form, $to, $subject, $comment);

          // }
      } catch (Exception $e){
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG11;
        }

    }
  }

}
debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

 ?>
<?php
$siteTitle = "パスワード変更 | 高校生バンド「Lazy Drop's」";
require('head.php');
?>

<body class="page-contact">

  <?php
  require('header.php');
  ?>

<div class="container">
  <section class="show-details">
    <div class="corner-title">
    <h2>パスワード編集</h2>
    </div>
    <div class="details">
      <div class="<?php if(!empty($success_msg)) echo 'area-msg-success'; ?> "><?php if(!empty($success_msg)) echo $success_msg; ?></div>
      <div class="article">
        <form action="" method="post" class="form-edit">
          <div class="area-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
          <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
            古いパスワード<input type="password" name="pass_old" class="edit-space">
          </label>
          <div class="area-msg"><?php if(!empty($err_msg['pass_old'])) echo $err_msg['pass_old']; ?></div>
          <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
            新しいパスワード<input type="password" name="pass_new" class="edit-space">
          </label>
          <div class="area-msg"><?php if(!empty($err_msg['pass_new'])) echo $err_msg['pass_new']; ?></div>
          <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
            新しいパスワード(再入力)<input type="password" name="pass_new_re" class="edit-space">
          </label>
          <div class="area-msg"><?php if(!empty($err_msg['pass_new_re'])) echo $err_msg['pass_new_re']; ?></div>
          <button type="submit" name="submit" class="btn-mypage">変更する</button>
        </form>

      </div>
      <?php
    require('sidebar.php');
       ?>
    </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
