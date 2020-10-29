<?php
    try {
        $db = new PDO('mysql:dbname=mini_bbs; host=localhost; charset=utf8', 'root', 'daisukeda20');
        print('DB接続：');
    } catch(PDOException $e) {
        print('DB接続エラー：' . $e->getmessage());
    }     
?>