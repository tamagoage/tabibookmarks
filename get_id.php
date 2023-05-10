<?php
// urlパラメーターを取得して代入
$url = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);

if (empty($url)) {
    header('Location: index.php');
    exit();
} else {
    // functions.phpを読み込む
    require_once('functions.php');
    $db = dbconnect();

    // urlパラメーターがtravels.urlと一致するものを取得
    $stmt = $db->prepare('SELECT id FROM travels WHERE travels.url = ?');
    if (!$stmt) {
        die($db->error);
    }
    //urlパラメーターをクエリにぶち込む 
    $stmt->bind_param('s', $url);
    $success = $stmt->execute();
    if(!$success) {
        die($db->error);
    }

    // dbから受け取った値を代入する変数を用意
    $stmt->bind_result($id);

    // $idをクッキーに保存
    while ($stmt->fetch()) {
        setcookie('id', $id, time()+60*60*24*14);
    }
}
?>