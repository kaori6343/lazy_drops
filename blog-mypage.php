<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

 ?>
<?php
$siteTitle = "Mypage | 高校生バンド「Lazy Drop's」";
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
      <div class="mypage-title">
      <h3>MY PAGE</h3>
      </div>
      <!-- <div class="logout">
        <a href="logout.php">ログアウト</a>
      </div> -->
      <article>
        <section class="blog">
          <h4>ROCK IN ONOKITA FES 2019</h4>
          <h5>更新日：2019.6.16</h5>
          <p>今日は引退ライブにご来場くださりありがとうございました。<br>最高のライブでした。<br>これから僕たちは受験勉強に入ります。<br>
            しばらくバンドを離れるのは寂しいですが、必ず戻ってくるので待っていてください。<br>今まで応援ありがとうございました。<br>
            また会いましょう！！<br>Lazy Drop's 一同</p>
        </section>
        <section class="blog">
          <h4>ROCK IN ONOKITA FES 2019</h4>
          <p></p>
        </section>
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
