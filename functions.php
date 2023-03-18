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

function get_travels_table_id($db, $value) {
    $stmt = $db->prepare('SELECT id FROM travels WHERE url = ?');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('s', $value);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    return $id;
}


?>