<?php
require('../functions.php');
require('../get_id.php');

$db = dbconnect();

// 画面に表示する処理
// urlパラメーターがtravels.urlと一致するものを取得
$stmt = $db->prepare('SELECT travels.id, schedules_id, destination, travel_dates, time, memo FROM schedules 
                        JOIN travels ON schedules.travels_r_id = travels.id
                        WHERE travels.url = ?');
if (!$stmt) {
    die($db->error);
}
// urlパラメーターを取得して代入
$url = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
$stmt->bind_param('s', $url);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}

// dbから受け取った値を代入する変数を用意
$stmt->bind_result($id, $schedules_id, $destination, $day, $time, $memo);

// 結果セットをメモリに格納する
$stmt->store_result();

// travelテーブルのidと紐づけるために変数に代入
$travels_r_id = $_COOKIE['id'];

$timeline = [];

// すべての行を取得する
while ($stmt->fetch()) {
    // 取得した行を変数に代入
    $timeObj = new DateTime($time); // $timeをDateTimeオブジェクトに変換
    $timeline[] = [
        'schedules_id' => $schedules_id,
        'time' => $timeObj->format('H:i'), // format()メソッドを使用して時刻をフォーマット
        'travel_dates' => $day,
        'destination' => $destination,
        'memo' => $memo
    ];
}

// 時間順にソート
usort($timeline, function ($a, $b) {
    return strtotime($a['time']) - strtotime($b['time']);
});


// クッキーから配列を取得
$travel_dates = json_decode($_COOKIE['travel_dates'], true);

// scheduleの削除
// delete関数用の変数用意
$schedules = 'schedules';
$schedules_id = 'schedules_id';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    delete($db, $schedules, $schedules_id, $delete_id);
    header('Location: schedule.php?id=' . $url);
    exit();
}

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
        <div class="header-site-menu">
            <nav class="site-menu">
                <ul>
                    <li><a href="schedule.php?id=<?php echo $url; ?>">schedule</a></li>
                    <li><a href="places.php?id=<?php echo $url; ?>">places</a></li>
                    <li><a href="checklists.php?id=<?php echo $url; ?>">list</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main">
        <div class="shiori">
            <div class="title">
                <h1>schedule</h1>
            </div>
            <!-- idへのリンク -->
            <div class="shiori-inner">
                <ul>
                    <?php foreach ($travel_dates as $travel_date) : ?>
                        <li><a href="#<?php echo h($travel_date); ?>"><?php echo h($travel_date); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="x-days">
                <!-- 日付ごとに場所を仕分ける -->
                <?php foreach ($travel_dates as $target_date) : ?>
                    <!-- 一行ずつ代入 -->
                    <?php foreach ($timeline as $timeline_while) : ?>
                        <?php if ($timeline_while['travel_dates'] == $target_date) : ?>
                            <div id="<?php echo h($target_date); ?>">
                                <section class="timeline">
                                    <div class="action_time"><?php echo h($timeline_while['time']); ?></div>
                                    <p class="action_title"><?php echo h($timeline_while['destination']); ?></p>
                                    <div class="action_memo">
                                        <p><?php echo h($timeline_while['memo']); ?></p>
                                    </div>
                                    <!-- 削除フォーム -->
                                    <form action="" method="post">
                                        <input type="hidden" name="delete_id" value="<?php echo $timeline_while['schedules_id']; ?>">
                                        <input type="submit" value="削除">
                                    </form>
                                </section>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <script>
                        // ページの読み込みが完了した時に実行される処理
                        window.addEventListener('DOMContentLoaded', function() {
                            // リンクがクリックされた時の処理
                            document.querySelectorAll('.shiori-inner ul li a').forEach(function(link) {
                                link.addEventListener('click', function(event) {
                                    // クリックされたリンクのhref属性の値を取得
                                    var targetId = this.getAttribute('href');

                                    // アクションエリアごとに処理を行う
                                    document.querySelectorAll('.x-days > div').forEach(function(actionArea) {
                                        // クリックされたリンクに対応するアクションエリアは表示し、それ以外は非表示にする
                                        if (actionArea.id === targetId.slice(1)) {
                                            actionArea.style.display = 'block';
                                        } else {
                                            actionArea.style.display = 'none';
                                        }
                                    });

                                    // ページ遷移をキャンセル
                                    event.preventDefault();
                                });
                            });
                        });
                    </script>
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
                            <?php for ($i = 0; $i < count($travel_dates); $i++) : ?>
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