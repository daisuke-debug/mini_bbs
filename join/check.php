<?php
	// セッションの開始
	session_start();
	// DataBase接続
	require('../db_connect.php');

	// セッションに値が入っていない場合
	if (!isset($_SESSION['join'])) {
		// index.phpへジャンプする
		header('Location: index.php');
		// このページを終了する
		exit();
	}

	// 再フォーム呼び出しの場合
    if (!empty($_POST)) {
        // SQL文作成 : Data Baseに登録
		$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW(), modified=NOW()');
        // SQL文実行
		echo $statement->execute(array(
			$_SESSION['join']['name'],
			$_SESSION['join']['email'],
			sha1($_SESSION['join']['password']),	// sha1 : 不可逆暗号
			$_SESSION['join']['image']
		));
		// セッション変数を削除する
		unset($_SESSION['join']);
		// thanks.phpへジャンプする 
		header('Location: thanks.php');
		// このページを終了する
		exit();
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<dd>
			<!-- [SESSON]ニックネームを表示 -->
			<?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES, 'UTF-8')); ?>
        </dd>
		<dt>メールアドレス</dt>
		<dd>
			<!-- [SESSON]メールアドレス -->
			<?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES, 'UTF-8')); ?>
        </dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
			<!-- [SESSON]写真など -->
			<?php if ($_SESSION['join']['image'] !== ''): ?>
				<img src = "../member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES, 'UTF-8')); ?>">
			<?php endif; ?>
		</dd>
	</dl>
	<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>

</div>
</body>
</html>
