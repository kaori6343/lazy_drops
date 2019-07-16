<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログアウトページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('ログアウトする。');
//セッション削除（ログアウトする）
session_destroy();
debug('ログインページへ遷移します。');

header("Location:blog.php");
 ?>
