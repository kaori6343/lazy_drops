<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ／アルバム　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//POST送信時処理
//================================


if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //画像をアップロードし、パスを格納
  if(!empty($_FILES['pic']['name'])){
    $pic = uploadImg($_FILES['pic'], 'pic');


    if(empty($err_msg)){

      try {

        $dbh = dbConnect();

        $sql = 'INSERT INTO images (pic, user_id, create_date) VALUES (:pic, :u_id, :date)';

        $data = array(':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

        debug('SQL：'.$sql);
        debug('流し込みデータ：'.print_r($data,true));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        //クエリ成功の場合
        if($stmt){
          $_SESSION['msg_success'] = SUC05;
          // $success_msg['pic'] = SUC05;
        }
      } catch (Exception $e){
        error_log('エラー発生：' .$e->getMessage());
        $err_msg['common'] = MSG11;
      }

    }
  }

}
//登録した画像一覧情報を取得
$images = getImgList();

 ?>
<?php
$siteTitle = "みんなのアルバム | 高校生バンド「Lazy Drop's」";
require('head.php');
?>

<body class="page-contact">

  <?php
  require('header.php');
  ?>

<div class="container">
  <section class="show-details">
    <div class="corner-title">
    <h2>アルバム</h2>
    </div>
    <div class="details">
      <div class="mypage-title">
      <h3>MY PAGE</h3>
      </div>

      <article>
        <div class="area-msg">
          <?php if(!empty($success_msg['pic'])) echo $success_msg['pic']; ?>
        </div>
        <div class="show-images">
          <?php
          foreach ($images as $value):
           ?>
             <div class="panel-list">
               <img src="<?php echo array_shift($value); ?>" alt="" class="img-list">
            </div>
            <div id="graydisplay"></div>
           <?php
         endforeach;
         ?>
          </div>
        <form action="" method="post" enctype="multipart/form-data">
        <div class="album-container">
          <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic" class="input-file">
            <img src="" alt="" class="prev-img">
            ドラッグ＆ドロップ
          </label>
          <div class="area-msg">
            <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
          </div>
          <button type="submit" name="submit" class="btn-mypage">保存する</button>
        </div>
      </form>
      </article>
      <?php
    require('sidebar.php');
       ?>
    </div>
  </section>
</div>
  <?php
  require('footer.php');
   ?>
