<?php
	// セッションの開始
	session_start();
	// DataBase接続
	require('../db_connect.php');

	// フォームがPOSTで送信された場合
	if (!empty($_POST)) {
		// ニックネームが空の場合
		if ($_POST['name'] === '') {
			// エラー配列にbrarnkを設定
			$error['name'] = 'brank';
		}

		// メールアドレスが空の場合
		if ($_POST['email'] === '') {
			// エラー配列にbrarnkを設定
			$error['email'] = 'brank';
		}

		// パスワードが空の場合
		if (strlen($_POST['password']) < 4) {
			// エラー配列にbrarnkを設定
			$error['password'] = 'length';
		}

		// パスワードが空の場合
		if ($_POST['password'] === '') {
			// エラー配列にbrarnkを設定
			$error['password'] = 'brank';
		}

		//
		$filename = $_FILES['image']['name'];
		// 画像がアップロードされている場合
        if (!empty($filename)) {
			// ファイル名の拡張子を取得する
			$ext = substr($filename, -3);
			// 拡張子がjpg, gif, png以外の場合
			if (($ext != 'jpg') && ($ext != 'gif') && ($ext != 'png')) {
				// エラー配列にtypeを設定
				$error['image'] = 'type';
			}
        }
		
		// アカウントの重複をチェックする
		if (empty($error)) {
			// SQL文準備(ユーザー入力) : emailが一致する数を取得する,SQL文作成
			$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
			// SQL文実行
			$member->execute(array($_POST['email']));
			// DBデータの取得
			$record = $member->fetch();
			// 一致したカウント数が1以上の場合(重複している)
			if ($record['cnt'] > 0) {
				// エラー配列にduplicateを設定
				$error['email'] = 'duplicate';
			}
		}

		// エラーが発生していない場合
		if (empty($error)) {
			// ファイル名に日付を付与(例:20201022151617xxxx.png) ※ファイル名が重複しないように日付時分秒を付与
			$image = date('YmdHis') . $_FILES['image']['name'];
			// アップロードされたファイルの保存場所を変更
			// 引数1:一時フォルダパス, 引数2:移動先のパス
			move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' .$image);

			// セッションに値を保存する
			$_SESSION['join'] = $_POST;
			// セッションに画像ファイル名を保存する
			$_SESSION['join']['image'] = $image;
			// check.phpへジャンプする
			header('Location: check.php');
			// このページを終了する
			exit();
		}
	}

	// URLパラメータがrewriteの場合 かつ セッションに値が入っている場合
	if (($_REQUEST['action'] == 'rewrite') && (isset($_SESSION['join']))) {
		// ポストに値を保存する
		$_POST = $_SESSION['join'];
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
<p>次のフォームに必要事項をご記入ください。</p>
<!-- ファイルのアップロードに必要なフォーム : enctype="multipart/form-data" -->
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars( $_POST['name'], ENT_QUOTES, 'UTF-8')); ?>" />
			<!-- ニックネームが空の場合 -->
			<?php if ($error['name'] == 'brank'): ?>
				<!-- Error Message -->
				<P class="error">*ニックネームを入力してください</p>
			<?php endif; ?>
		</dd>

		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars( $_POST['email'], ENT_QUOTES, 'UTF-8')); ?>" />
			<!-- メールアドレスが空の場合 -->
			<?php if ($error['email'] == 'brank'): ?>
				<!-- Error Message -->
				<P class="error">*メールアドレスを入力してください</p>
			<?php endif; ?>
			<!-- エラー配列にduplicateがある場合 -->
			<?php if ($error['email'] == 'duplicate'): ?>
				<!-- Error Message -->
				<P class="error">*指定されたメールアドレスは既に登録されています</p>
			<?php endif; ?>

		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars( $_POST['password'], ENT_QUOTES, 'UTF-8')); ?>" />
			<!-- パスワードが空の場合 -->
			<?php if ($error['password'] == 'brank'): ?>
				<!-- Error Message -->
				<P class="error">*パスワードを入力してください</p>
			<?php endif; ?>
			<?php if ($error['password'] == 'length'): ?>
				<!-- Error Message -->
				<P class="error">*パスワードは4文字以上で入力してください</p>
			<?php endif; ?>
        </dd>

		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
			<!-- 画像ファイル選択が異常の場合 -->
			<?php if ($error['image'] == 'type'): ?>
				<!-- Error Message -->
				<P class="error">*写真などは「.jpg」または「.gif」または「.png」を指定してください</p>
			<?php endif; ?>
			<!-- Errorが発生している場合 -->
			<?php if (!empty($error)): ?>
				<!-- Error Message -->
				<P class="error">*恐れ入りますが、画像を再度入力してください</p>
			<?php endif; ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
