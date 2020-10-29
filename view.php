
<?php
	// セッションの開始
  session_start();
	// DataBase接続
  require('db_connect.php');

  // URLパラメータidが空の場合 : view.php直接呼出し時対応
  if (empty($_REQUEST['id'])) {
    // login.phpへジャンプする
    header('Location: index.php');
    // このページを終了する
    exit();
  }

  // SQL文作成 : membersテーブルid=postsテーブルmem_idが一致する かつ postsテーブルid(メッセージID)が一致するレコードを取得する
  $posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.mem_id AND p.id=?');
  // SQL文実行(postsテーブルid)
  $posts->execute(array($_REQUEST['id']));

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  <!-- [一覧にもどる]リンク URLパラメータ "index.php?[id]" "-->
  <p>&laquo;<a href="index.php<?php print(htmlspecialchars($post['id'], ENT_QUOTES)) ?>">一覧にもどる</a></p>

  <?php if( $post = $posts->fetch()): ?>
    <div class="msg">
    <img src="member_picture/" />
    <p><?php print('post[id]：' . $post['id']); ?></P>
    <?php print(htmlspecialchars($post['message'])); ?>
    <p><span class="name">（<?php print(htmlspecialchars($post['created'])); ?>）</span></p>
    <p class="day"></p>
    </div>
  <?php else: ?>
  	<p>その投稿は削除されたか、URLが間違えています</p>
  <?php endif; ?>
  </div>
</div>
</body>
</html>
