<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　登録情報変更ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報；'.print_r($dbFormData,true));

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST送信：'.print_r($_POST, true));

  $username = $_POST['username'];
  $email = $_POST['email'];
  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['username'] !== $username){

    //nameの最大文字数チェック
    validMaxLen($username, 'username');
    //nameの未入力チェック
    validRequired($username, 'username');
}
  if($dbFormData['email'] !== $email){
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen1($email, 'email');
    //emailの重複チェック
    validEmailDup($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
}
    if(empty($err_msg)){
      debug('バリデーションOK。');

      try {
        $dbh = dbConnect();

        $sql = 'UPDATE users SET username = :u_name, email = :email WHERE id = :u_id';

        $data = array(':u_name' => $username, ':email' => $email, ':u_id' => $dbFormData['id']);

        $stmt = queryPost($dbh, $sql, $data);

        // if($stmt){
        //   debug('クエリ成功。');
        //   $success_msg = SUC02;
        //
        // }else{
        //   debug('クエリに失敗しました。');
        //   $err_msg['common'] = MSG11;
        // }
      }catch (Exception $e){
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG11;
      }
    }
}
debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

 ?>
<?php
$siteTitle = "登録情報変更 | 高校生バンド「Lazy Drop's」";
require('head.php');
?>

<body class="page-contact">

  <?php
  require('header.php');
  ?>

<div class="container">
  <section class="show-details">
    <div class="corner-title">
    <h2>登録情報変更</h2>
    </div>
    <div class="details">
      <div class="<?php if(!empty($success_msg)) echo 'area-msg-success'; ?>"><?php if(!empty($success_msg)) echo $success_msg; ?></div>
      <div class="article">
        <form action="" method="post" class="form-edit">
          <div class="area-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
          <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
            名前<input type="text" name="username" class="edit-space" value="<?php echo getFormData('username'); ?>">
          </label>
          <div class="area-msg"><?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?></div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email<input type="text" name="email" class="edit-space" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></div>
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
