<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//POST送信されたいた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  try{
    $dbh = dbConnect();

    $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';

    $data = array(':us_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){

      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION, true));
      debug('トップページへ遷移します。');
      header("Location:blog.php");
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG11;
    }
  } catch (Exception $e){
    error_log('エラー発生：' .$e->getMessage());
    $err_msg['common'] = MSG11;
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
     <h2>FAN CLUB</h2>
     </div>
     <div class="details">
       <form action="" method="post">
         <button type="submit" name="submit" class="btn-mypage">退会する
         </button>
         <div class="area-msg">
           <?php if(!empty($err_msg['common'])) echo $err_msg['common']
            ?>
        </div>
       </form>
     </div>
   </section>
 </div>
<?php
require('footer.php');
 ?>
