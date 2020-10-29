
<?php
/**
 * メッセージ削除 ページ
 * @Description ひとことメッセージをデータベースから削除する
 * @package     logout.php
 * @license     なし
 * @link        login.php
 * @version     1.0.0
 */

  // セッションの開始
  session_start();
	// DataBase接続
  require('db_connect.php');

   // セッション[id]に値が入っている場合
   if (isset($_SESSION['id'])) {
        $id = $_REQUEST['id'];

        $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
        $messages->execute(array($id));
        $message = $messages->fetch();

        if ($message['mem_id'] == $_SESSION['id']){
            $del = $db->prepare('DELETE FROM posts WHERE id=?');
            $del->execute(array($id));
        }
   }

   // index.phpへジャンプする
   header('Location: index.php');
   // このページを終了する
   exit();
?>
