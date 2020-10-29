
<?php
	// セッションの開始
	session_start();
	// DataBase接続
  require('db_connect.php');

  // クッキーが空以外の場合
  if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
  }

  // 再フォーム呼び出しの場合
  if (!empty($_POST)) {
    // クッキーの値をポストで上書きする
    $email = $_POST['email'];

    // emailが空以外の場合　かつ　passwordが空以外の場合
    if (($_POST['email'] !== '') && ($_POST['password'] !== '')) {
      // SQL文作成 : DataBaseからemailとパスワードが一致するレコードを取得する
      $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
      // SQL文実行
      $login->execute(array($_POST['email'],sha1($_POST['password'])));
      // memberへSQL文実行情報を取得する
      $member = $login->fetch();
      // 情報が取得できた場合
      if ($member) {
          // セッションに値を保存する
          $_SESSION['id'] = $member['id'];
          // セッションに値を保存する
          $_SESSION['time'] = time();
          // 次回自動ログインチェックボックスにチェックがある場合
          if ($_POST['save'] === 'on') {
            // クッキーに値を保存する(有効期限:14日間=3600分*24時間*14日)
            setcookie('email', $_POST['email'], time()+3600*24*14);
          }
          // index.phpへジャンプする
          header('Location: index.php');
          // このページを終了する
          exit();
      }
      // 情報が取得できない場合
      else {
        // エラー配列にfailedを設定
        $error['login'] = 'failed';
      }
    }
    // emailが空の場合　または　passwordが空の場合
    else {
      // エラー配列にblankを設定
        $error['login'] = 'blank';    
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- 外部スタイルシートリンク -->
<link rel="stylesheet" type="text/css" href="style.css" />
<title>ログインする</title>
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ログインする</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/index.php">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>" />
          <!-- エラー配列にbrankがある場合 -->
          <?php if ($error['login'] == 'blank'): ?>
            <!-- Error Message -->
            <P class="error">*メールアドレスとパスワードをご記入ください</p>
          <?php endif; ?>
          <!-- エラー配列にfailedがある場合 -->
          <?php if ($error['login'] == 'failed'): ?>
            <!-- Error Message -->
            <P class="error">*ログインに失敗しました。正しくご記入ください</p>
          <?php endif; ?>
        </dd>
        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password']); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
        <input type="submit" value="ログインする" />
      </div>
    </form>
  </div>
  <div id="foot">
    <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
  </div>
</div>
</body>
</html>
