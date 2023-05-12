<?php
require('../functions.php');
require('../get_id.php');

$db = dbconnect();
// 画面に表示する処理
// urlパラメーターがtravels.urlと一致するものを取得
$stmt = $db->prepare('SELECT travels.id, destination, time, /*transportation, */memo FROM schedules 
                        JOIN travels ON schedules.travels_r_id = travels.id
                        WHERE travels.url = ?');    
if (!$stmt) {
        die($db->error);
}
// urlパラメーターを取得して代入
$url = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
$stmt->bind_param('s', $url);
$success = $stmt->execute();
if(!$success) {
    die($db->error);
}

// dbから受け取った値を代入する変数を用意
$stmt->bind_result($id, $destination, $time, $memo);

// 結果セットをメモリに格納する
$stmt->store_result();

// travelテーブルのidと紐づけるために変数に代入
$travels_r_id = $_COOKIE['id'];

$timeline = []; 

// すべての行を取得する
while ($stmt->fetch()) {
    // 取得した行を変数に代入
    $timeline[] = [
            'time' => $time,
            'destination' => $destination,
            'memo' => $memo
        ];
}

// クッキーから配列を取得
$travel_dates = json_decode($_COOKIE['travel_dates'], true);

// scheduleの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['destination'] = filter_input(INPUT_POST, 'destination', FILTER_DEFAULT);
    if ($form['destination'] === "") {
        $error['destination'] = 'blank';
    }

    $form['memo'] = filter_input(INPUT_POST, 'memo', FILTER_DEFAULT);

    $form['travel_dates'] = filter_input(INPUT_POST, 'travel_dates', FILTER_DEFAULT);

    $form['time'] = filter_input(INPUT_POST, 'time', FILTER_DEFAULT);
    if ($form['time'] === "") {
        $error['time'] = 'blank';
    }

    $form['googlemap_url'] = filter_input(INPUT_POST, 'googlemap_url', FILTER_DEFAULT);


    // dbに登録
    $stmt_while = $db->prepare('insert into schedules (travels_r_id, destination, travel_dates, time, memo) values(?,?,?,?,?)');
    if (!$stmt_while) {
        die($db->error);
    }

    $stmt_while->bind_param('issss', $travels_r_id, $form['destination'], $form['travel_dates'], $form['time'], $form['memo']);
    $success = $stmt_while->execute();
    if (!$success) {
        die($db->error);
    }
    $stmt_while->close(); // 追加した行

    header('Location: schedule.php?id=' . $url);
    exit();
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
    <title>schedule</title>
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
                    <li><a href="schedule.php?id=<?php echo $url; ?>">schedule</a></li>
                    <li><a href="places.php?id=<?php echo $url; ?>">places</a></li>
                    <li><a href="checklists.php?id=<?php echo $url; ?>">list</a></li>
                </ul>
            </nav>
        </div>
        <div class="shiori">
            <div class="title">
                <h1>schedule</h1>
            </div>
            <div class="x-days">
                <!-- 一行ずつ代入 -->
                
                    <?php foreach ($timeline as $timeline_while): ?>
                <section class="timeline">
                    <div class="action_time"><?php echo h($timeline_while['time']); ?></div>
                    <p class="action_title"><?php echo h($timeline_while['destination']); ?></p>
                    <div class="action_memo">
                        <p><?php echo h($timeline_while['memo']); ?></p>
                    </div>
                </section>
                    <?php endforeach; ?>
                
                <div class="open-area">
                    <label class="open" for="pop-up">✙</label>
                </div>
            </div>
        </div>
        <input type="checkbox" id="pop-up">
        <div class="overlay">
            <div class="window">
                <label class="close" for="pop-up">×</label>
                <form action="" method="post">
                    <dl class="form-area">
                    <select name="travel_dates" id="select_days">
                        <?php for ($i = 0; $i < count($travel_dates); $i++): ?>
                            <option value="<?php echo h($travel_dates[$i]); ?>"><?php echo h($travel_dates[$i]); ?></option>
                        <?php endfor; ?>
                    </select>
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
                            <dd><input class="textarea" type="time" name="time"></dd>
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