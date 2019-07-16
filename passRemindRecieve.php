<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行認証キー入力ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//SESSIONに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
  header("Location:passRemindSend.php");
  //認証キー送信ページへ
}
//================================
// 画面処理
//================================
//post送信されている場合

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：' .print_r($_POST,true));

  //変数に認証キーを代入
  $auth_key = $_POST['token'];

  //未入力チェック
  validRequired($auth_key, 'token');

  if(empty($err_msg)){
    debug('未入力チェックok');

    //固定長チェック
    validLength($auth_key, 'token');
    //半角チェック
    validHalf($auth_key, 'token');

    if(empty($err_msg)){
      debug('バリデーションok');

      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG16;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG17;
      }

      if(empty($err_msg)){
        debug('認証OK。');

        $pass = makeRandKey(); //パスワード生成
        //例外処理
      try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'UPDATE users SET pass = :pass WHERE email = :email AND delete_flg = 0';

        $data = array(':email' => $_SESSION['email'], ':pass' =>　password_hash($pass, PASSWORD_DEFAULT));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt) {
          debug('クエリ成功。');

          //メールを送信
          $form = 'info@lazydrops.com';
          $to = $_SESSION['email'];
          $subject = "【パスワード再発行完了】｜ Lazy Drop's";
          $comment = <<<EOT
本メールアドレス宛にパスワード再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：
http://localhost:8888/band/blog.php
再発行パスワード：{$pass}
*ログイン後パスワードのご変更をお願いいたします。

//////////////////////////////////////
Lazy Drop's 運営事務局
E-mail info@lazydrops.com
//////////////////////////////////////
EOT;
          sendMail($form, $to, $subject, $comment);

          //セッション削除
          session_unset();
          $_SESSION['msg_success'] = SUC04;
          debug('セッション変数の中身：'.print_r($_SESSION, true));

          header("Location:blog.php");

        }else{
          debug('クエリに失敗しました。');
          $err_msg['common'] = MSG011;
        }

      } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG11;
      }
    }
  }
}
}
 ?>
<?php
$siteTitle = "パスワード再発行認証 | 高校生バンド「Lazy Drop's」";
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
      <p class="passremind">ご指定のメールアドレスお送りした【パスワード再発行認証】メール内にある<br>「認証キー」をご入力ください。</p>
     <p class="err_msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></p>
     <form action="" method="post" class="login-area">
       <label class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
       <input type="text" name="token" placeholder="認証キー"　value="<?php echo getFormData('token'); ?>" class="login-space">
       </label>
       <div class="err_msg"><?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?></div>
       <button type="submit" name="submit" class="login-btn">再発行する</button>
     </form>
    </div>
    <div class="login-msg">
    <p>メールが届かない場合は入力いただいたメールアドレスに誤りがある可能性がございます。<br>
      <a href="passRemindSend.php">&lt;&lt; パスワード再発行メールを再度送信する</a></p>
  </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
