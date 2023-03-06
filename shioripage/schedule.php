<?php
require('../functions.php');
$db = dbconnect();
// urlパラメーターがtravels.urlと一致するものを表示 必須
$stmt = $db->prepare('SELECT travels.id, destination, time, /*transportation, */memo FROM schedules 
                        JOIN travels ON schedules.travels_r_id = travels.id
                        WHERE travels.url = ?');    
if (!$stmt) {
        die($db->error);
}
// urlパラメーターの?=以降を取得 必須
$url = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
$stmt->bind_param('s', $url);
$success = $stmt->execute();
if(!$success) {
    die($db->error);
}

// dbから受け取った値を代入する変数を"用意" 必須
$stmt->bind_result($travels_r_id, $destination, $time,/* $transportation,*/ $memo);
$stmt->execute();
$stmt->store_result();
// この下にあるfetchが悪さをしている！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
// $stmt->fetch();
// scheduleの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['destination'] = filter_input(INPUT_POST, 'destination', FILTER_DEFAULT);
    if ($form['destination'] === "") {
        $error['destination'] = 'blank';
    }

    $form['memo'] = filter_input(INPUT_POST, 'memo', FILTER_DEFAULT);

    $form['time'] = filter_input(INPUT_POST, 'time', FILTER_DEFAULT);
    if ($form['time'] === "") {
        $error['time'] = 'blank';
    }

    $form['googlemap_url'] = filter_input(INPUT_POST, 'googlemap_url', FILTER_DEFAULT);
    
    // dbに登録
    $stmt = $db->prepare('insert into schedules (travels_r_id, destination, time, memo) values(?,?,?,?)');
    if(!$stmt) {
        die($db->error);
    }

    // travelテーブルのidと紐づける テスト
    $travels_r_id = 1;

    $stmt->bind_param('isss', $travels_r_id, $form['destination'], $form['time'], $form['memo']);
    $success = $stmt->execute();
    if(!$success) {
        die($db->error);
    }
}
?>
<!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../common.css">
    <link rel="stylesheet" href="css/schedule.css">
    <title>基本となるページ</title>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <h1>LOGO</h1>
        </div>
    </header>
    <main class="main">
        <div class="header-site-menu">
            <nav class="site-menu">
                <ul>
                    <li><a href="schedule.html">schedule</a></li>
                    <li><a href="places.html">places</a></li>
                    <li><a href="checklists.html">list</a></li>
                </ul>
            </nav>
        </div>
        <div class="shiori">
            <div class="title">
                <h1>schedule</h1>
            </div>
            <div class="x-days">
                <!-- 一行ずつ代入 -->
                <?php while ($stmt->fetch()): ?>
                <section class="timeline">
                    <div class="action_time"><?php echo h($time); ?></div>
                    <p class="action_title"><?php echo h($destination); ?></p>
                    <div class="action_memo">
                        <p><?php echo h($memo); ?></p>
                    </div>
                </section>
                <?php endwhile; ?>
                <div class="open-area">
                    <label class="open" for="pop-up">✙</label>
                </div>
            </div>
        </div>
        <input type="checkbox" id="pop-up">
        <div class="overlay">
            <div class="window">
                <label class="close" for="pop-up">×</label>
                <h3>?日目</h3>
                <form action="" method="post">
                    <dl class="form-area">
                        <label for="destination">
                            <dt>場所</dt>
                            <dd><input class="textarea" type="text" name="destination"></dd>
                        </label>
                        <label for="memo">
                            <dt>メモ</dt>
                            <dd><input class="textarea" type="text" name="memo"></dd>
                        </label>
                        <label for="time">
                            <dt>時間</dt>
                            <dd><input class="textarea" type="datetime" name="time"></dd>
                        </label>
                        <label for="googlemap_url">
                            <dt>googlemap</dt>
                            <dd><input class="textarea" type="text" name="googlemap_url"></dd>
                        </label>
                        <button type="submit">追加する</button>
                    </dl>
                </form>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="copyright">@tabibookmarks</div>
    </footer>
</body>
</html>