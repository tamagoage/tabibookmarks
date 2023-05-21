<?php 
// 環境変数を取得
$host = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');

function h($value) {
    return htmlspecialchars($value,ENT_QUOTES);
}

// dbへの接続
function dbconnect() {
    global $host, $username, $password, $database;
    $db = new mysqli($host, $username, $password, $database);
    if ($db->connect_errno) {
        die("Failed to connect to MySQL: " . $db->connect_error);
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

// テーブルを削除する
function delete($db, $table, $id_name, $id) {
    $stmt = $db->prepare('DELETE FROM ' . $table . ' WHERE '.$id_name.' = ?');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bind_param('i', $id);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    $stmt->close();
}
?>