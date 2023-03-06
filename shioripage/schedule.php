<?php
require('../functions.php');
$db = dbconnect();
// urlパラメーターが一致するものを表示
$stmt = $db->prepare('SELECT destination, time, transportation, memo FROM schedules 
                        JOIN pages_info ON schedules.schedules_id = pages_info.id
                        JOIN travels ON pages_info.id = travels.id
                        WHERE travels.url = ?');    
if (!$stmt) {
        die($db->error);
}
$url = filter_input(INPUT_GET, 'id', FILTER_DEFAULT);
$stmt->bind_param('s', $url);
$success = $stmt->execute();
if(!$stmt) {
    die($db->error);
}
// dbから受け取った値を変数に代入
$stmt->bind_result($destination, $time, $transportation, $memo);
$stmt->fetch();
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
            <h1>LOGO<?php echo h($destination); ?></h1>
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
                <section class="timeline">
                    <div class="action_time">8:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">9:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">8:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">9:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">8:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">9:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">8:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
                <section class="timeline">
                    <div class="action_time">9:00</div>
                    <p class="action_title">九州</p>
                    <div class="action_memo">
                        <p>飛行機のチケット</p>
                    </div>
                </section>
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
                <form action="#">
                    <dl class="form-area">
                        <label for="places">
                            <dt>場所</dt>
                            <dd><input class="textarea" type="text" name="places"></dd>
                        </label>
                        <label for="time">
                            <dt>時間</dt>
                            <dd><input class="textarea" type="datetime" name="s_time"></dd>
                            <dd><input class="textarea" type="datetime" name="e_time"></dd>
                        </label>
                        <label for="googlemap_url">
                            <dt>googlemap</dt>
                            <dd><input class="textarea" type="text" name="googlemap"></dd>
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