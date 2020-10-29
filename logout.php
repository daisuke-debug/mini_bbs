
<?php
/**
 * ログアウト ページ
 * @package     logout.php
 * @Description ログアウト時にセッションの情報とcookieの情報を削除し、ログイン画面に戻る
 * @license     なし
 * @link        login.php
 * @version     1.0.0
 */
    // セッションの開始
    session_start();
    // セッションの情報を削除
    $_SESSION = array();
    // Cookieを使用してもいいか : default(1) ※0にしてはいけない
    if (ini_get('session.use_cookies')) {
        // セッションクッキーのパラメータを取得する
        $params = session_get_cookie_params();
        //  クッキー削除を送信する
        setcookie(session_name() . '', time() - 4200, $params['domain'], $params['secure'], $params['httponly']);
    }
    // セッションのデータを破棄
    session_destroy();
    // クッキー情報の削除
    setcookie('email', '', time()-3600);
    setcookie('password', '', time()-3600);

   // login.phpへジャンプする
   header('Location: login.php');
   // このページを終了する
   exit();
?>
