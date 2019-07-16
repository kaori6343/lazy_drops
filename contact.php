<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

require('function.php');
  if(!empty($_POST)){
  $name = $_POST['name'];
  $email = $_POST['email'];
  $comment = $_POST['comment'];

  // 未入力チェック
    validRequired($name, 'name');
    validRequired($email, 'email');
    validRequired($comment, 'comment');

    if(empty($err_msg)){

  // Emailの形式チェック
  validEmail($email, 'email');
  // Emailの最大文字数チェック
  validMaxLen1($email, 'email');
  // textareaの最大文字数チェック
  validMaxLen2($comment, 'comment');

  if(empty($err_msg)){
    $from = 'kaoriii.589@gmail.com';
    $to = $_POST['email'];
    $subject = '【お問い合わせを受け付けました】｜Lazy Drop"s';
    $comment = $_POST['comment'];


  sendMail($from, $to, $subject, $comment);
}
}
}
?>
<?php
$siteTitle = "Contact | 高校生バンド「Lazy Drop's」";
require('head.php');
?>

<body class="page-contact">

  <?php
  require('header.php');
  ?>

<div class="container">
  <section class="show-details">
    <div class="corner-title">
    <h2>CONTACT</h2>
    </div>
    <form action="" method="post">
      <div class="form-group">
        <span class="err_msg"><?php if(!empty($err_msg['name']))echo $err_msg['name']; ?></span>
          <input type="text" name="name" class="valid-text" placeholder="名前" value="<?php if(!empty($_POST['name']))echo $_POST['name']; ?>">
      </div>
      <div class="form-group">
      <span class="err_msg"><?php if(!empty($err_msg['email']))echo $err_msg['email']; ?></span>
          <input type="text" name="email" class="valid-email" placeholder="email" value="<?php if(!empty($_POST['email']))echo $_POST['email']; ?>">
      </div>
      <div class="form-group">
      <span class="err_msg"><?php if(!empty($err_msg['comment']))echo $err_msg['comment']; ?></span>
          <textarea name="comment" cols="100" rows="10" id="count-text" class="valid-textarea" placeholder="200文字以内で入力ください。"><?php if(!empty($_POST['comment']))echo $_POST['comment']; ?></textarea>
          <div class="count-countainer"><span class="show-count-text" class="help-block">0</span>/200</div>
      </div>
      <input type="submit" name="submit" value="送信" class="submit">
    </form>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
