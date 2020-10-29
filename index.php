
<?php
/**
 * ひとこと掲示板 ページ
 * @Description ひとこと掲示板メインページ
 * @package     index.php
 * @license     なし
 * @link        logout.php, index.php, view.php, delete.php
 * @version     1.0.0
 */

	// セッションの開始
  session_start();
	// DataBase接続
  require('db_connect.php');

  // セッション[id]に値が入っている場合 かつ セッション[time]から1時間以内の場合
  if ((isset($_SESSION['id'])) && ($_SESSION['time'] + 3600 > time())) {
    // セッション[time]を現時刻で上書きしておく
    $_SESSION['time'] = time();
    // SQL文作成 : membersテーブルid=_SESSION[id]が一致するレコードを取得する
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    // SQL文実行
    $members->execute(array($_SESSION['id']));
    // membersへSQL文実行情報を取得する
    $members = $members->fetch();
  }
  // セッション[id]に値が入っていない場合 または セッション[time]から1時間経過している場合
  else {
    // login.phpへジャンプする
    header('Location: login.php');
    // このページを終了する
    exit();
  }

  // リクエスト変数[page]からページ数を取得
  $page = $_REQUEST['page'];

  // ページ数がnullの場合
  if ($page == '') {
    // ページ数に1を設定
    $page = 1;
  }
  // ページ数がnull以外の場合
  else {
      // ページ数 = 1以上の値を取得
      $page = max($page, 1);
  }

  // SQL文作成 : DataBaseからpostsテーブルのカウント数を取得する
  $counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
  // SQL文実行
  $cnt = $counts->fetch();
  // 最大ページ数 = postsテーブルのカウント数 / 5 : 少数は切り上げ
  $maxPage = ceil($cnt['cnt'] / 5);
  // ページ数 = maxPage以下の値を取得
  $page = min($page, $maxPage);
  // 1ページに5件の情報を表示する演算を実行
  $start = ($page - 1) * 5;

  // SQL文作成 : DataBaseからpostsテーブルの該当するレコードを5件分取得する
  $posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.mem_id ORDER BY p.created DESC LIMIT ?,5');
  // 指定された変数名にパラメータをバインドする
  $posts->bindParam(1, $start, PDO::PARAM_INT);
  // SQL文実行
  $posts->execute();

  //------------------------------------------------------
  // 「投稿する」ボタンが押された場合
  //------------------------------------------------------
  if (!empty($_POST)) {
    // メッセージが空でなければ
    if ($_POST['message'] !== '') {

      if (empty($_POST['reply_post_id'])) {
        print('test1');
        $replyPostId = 1;
      }
      else {
        print('test2');
        $replyPostId = $_POST['reply_post_id'];
      }

      // SQL文作成 : DataBaseへ情報を保存する
      $message = $db->prepare('INSERT INTO posts SET message=?, mem_id=?, reply_message_id=?, created=NOW(), modified=NOW()');
      // SQL文実行
      $message->execute(array(
        $_POST['message'],
        $members['id'],
        $replyPostId 
      ));

      // index.phpへジャンプする
      header('Location: index.php');
      // このページを終了する
      exit();
    }
  }

  //------------------------------------------------------
  // [Re]ボタンがクリックされた場合
  //------------------------------------------------------
  if (isset($_REQUEST['res'])){
    // SQL文作成 :  membersテーブルid=postsテーブルmem_idが一致する かつ postsテーブルid(メッセージID)が一致するレコードを取得する
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.mem_id AND p.id=?');
    // SQL文実行
    $response->execute(array($_REQUEST['res']));
    // tableへSQL文実行情報を取得する
    $table=$response->fetch();
    // messageへ　DataBaseから、「＠'name'　　'message'」のフォーマットで文字列を作成する。
    $message = '@' . $table['name'] . ' ' . $table['message'];  
    print('reply_message=：'.$message);
  }
  else {
    $message = "";
  }
  
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
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php print(htmlspecialchars($members['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <!-- Message Boxに文字列('message'変数)を表示する -->
          <textarea name="message" cols="50" rows="5"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>"/>
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>

    <!-- postsテーブル5件分の要素分ループする　-->
    <?php foreach($posts as $post): ?>
      <div class="msg">
        <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" width="48" height="48" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
        <!-- <段落> (postsテーブル[message])"-->
        <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>
          <!-- <インライン要素> -->
          <span class="name">
            <!-- (postsテーブル[name]) -->
            <?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>
          </span>
          <!-- [Re]リンク "index.php?(POST)res=postsテーブルid"-->
          [<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)) ?>">Re</a>]          
        </p>
        <!-- <段落> -->
        <p class="day">
          <!-- <段落> [日付]リンク URLパラメータ "view.php?(postsテーブル[id])"-->
          <a href="view.php?id=<?php print(htmlspecialchars($post['id'])); ?>">
            <?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?>      
          </a>
          <?php if ($post['reply_message_id'] > 0): ?>
            <!-- <段落> 返信元のメッセージリンク URLパラメータ "view.php?(postsテーブル[reply_message_id])"-->
            <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'])); ?>">返信元のメッセージ</a>
          <?php endif; ?>
          <!-- セッション[id]と (postsテーブル[mem_id])が等しい場合-->
          <?php if ($_SESSION['id'] == $post['mem_id']): ?>
            <!-- <段落> [削除]リンク URLパラメータ "delete.php?[id](postsテーブル[id])"-->
            [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>" style="color: #F33;">削除</a>]
          <?php endif; ?>
        </p>
      </div>
    <?php endforeach; ?>

    <ul class="paging">
      <?php if ($page >1): ?>
        <li><a href="index.php?page=<?php print($page-1); ?>">前のページへ</a></li>
      <?php else: ?>
        <li><a href="">前のページへ</a></li>
      <?php endif; ?>
      <?php if ($page < $maxPage): ?>
        <li><a href="index.php?page=<?php print($page+1); ?>">次のページへ</a></li>
      <?php else: ?>
        <li><a href="">次のページへ</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
</body>
</html>
