<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//post送信されている場合

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：' .print_r($_POST,true));

  $email = $_POST['email'];

  //未入力チェック
  validRequired($email, 'email');

  if(empty($err_msg)){
    debug('未入力チェックok');

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen1($email, 'email');

    if(empty($err_msg)){
      debug('バリデーションok');

      try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';

        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //EmailがDBへ登録されている場合
        if($stmt && array_shift($result)) {
          debug('クエリ成功。DB登録あり。');
          $_SESSION['msg_success'] = SUC04;

          $auth_key = makeRandKey(); //認証キー生成　

          //メールを送信
          $form = 'info@lazydrops.com';
          $to = $email;
          $subject = "【パスワード再発行認証】｜ Lazy Drop's";
          $comment = <<<EOT
本メールアドレスあにパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：
http://localhost:8888/band/passRemindRecieve.php
認証キー：{$auth_key}
*認証キーの有効期限は30分となります。

認証キーを再発行されたい場合は下記ページより再度再発行をお願いいたします。
http://localhost:8888/band/passRemindSend.php

//////////////////////////////////////
Lazy Drop's 運営事務局
E-mail info@lazydrops.com
//////////////////////////////////////
EOT;
          sendMail($form, $to, $subject, $comment);

          //認証に必要な情報をセッションへ保存
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['email'] = $email;
          $_SESSION['auth_key_limit'] = time()+(60*30); //現在時刻より30分後のUNIXタイムスタンプを入れる
          debug('セッション変数の中身：'.print_r($_SESSION, true));

          header("Location:passRemindRecieve.php");

        }else{
          debug('クエリに失敗したかDBに登録のないEmailが入力されました。');
          $err_msg['common'] = MSG011;
        }

      } catch (Exception $e) {
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG11;
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
    <h2>BLOG</h2>
    </div>
    <div class="details">
      <p class="passremind">ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します。</p>
     <p class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></p>
     <form action="" method="post" class="login-area">
       <input type="text" name="email" placeholder="登録のメールアドレス" class="login-space">
       <button type="submit" name="submit" class="login-btn">送信する</button>
     </form>
    </div>
    <div class="login-msg">
    <p>※登録のメールアドレスを忘れた場合は、初めから会員登録をやり直してください。</p>
  </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
