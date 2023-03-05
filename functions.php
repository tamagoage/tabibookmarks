<?php 
function h($value) {
    return htmlspecialchars($value,ENT_QUOTES);
}

// dbへの接続
function dbconnect() {
    $db = new mysqli('localhost:8889','root','root','tabi');
    if (!$db) {
		die($db->error);
	}

    return $db;
}

// ランダムな文字列を生成
function generateRandomString($length = 40) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charLength = strlen($chars);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $chars[rand(0, $charLength - 1)];
    }
    return $randomString;
}

?>