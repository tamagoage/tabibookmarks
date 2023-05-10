<?php
include("../functions.php");
include("../get_id.php");
$db = dbconnect();
// 画面に表示する処理
// urlパラメーターがtravels.urlと一致するものを取得
$stmt = $db->prepare('SELECT travels.id, list FROM checklists
                        JOIN travels ON checklists.travels_r_id = travels.id
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
$stmt->bind_result($id, $list);

// 結果セットをメモリに格納する
$stmt->store_result();

// travelテーブルのidと紐づけるために変数に代入
$travels_r_id = $id;

$checklists = [];

// すべての行を取得する
while ($stmt->fetch()) {

    // 取得した行を変数に代入
    $checklists[] = [
        'list' => $list
    ];
}

// checklistsの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['list'] = filter_input(INPUT_POST, 'list', FILTER_DEFAULT);
    if ($form['list'] === "") {
        $error['list'] = 'blank';
    }

    // dbに登録
    if (empty($error)) {
        $stmt_while = $db->prepare('INSERT INTO checklists (travels_r_id, list) values(?,?)');
        if (!$stmt_while) {
            die($db->error);
        }

        $stmt_while->bind_param('is', $travels_r_id, $form['list']);
        $success = $stmt_while->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt_while->close();

        // リロード対策
        header('Location: checklists.php?id=' . $url);
        exit();
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
    <link rel="stylesheet" href="css/checklists.css">
    <title>チェックリストのページ</title>
    <!-- jqueryの読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- jquery.cookieの読み込み -->
    <script src="jquery.cookie.js"></script>
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
                <h1>checklists</h1>
            </div>
            <div class="checklist-area">
                <fieldset class="checkbox-002">
                    <?php foreach ($checklists as $checklist): ?>
                    <label>
                        <input type="checkbox" name="checkbox-002" value="<?php echo h($checklist['list']); ?>">
                        <?php echo h($checklist['list']); ?>
                    </label>
                    <?php endforeach; ?>
                </fieldset>
                <div class="open-area">
                    <label class="open" for="pop-up">✙</label>
                </div>
            </div>
            <input type="checkbox" id="pop-up">
            <div class="overlay">
                <div class="window">
                    <label class="close" for="pop-up">×</label>
                    <form action="" method="post">
                        <label for="list">メモの内容<input type="text" name="list"></label>
                        <button type="submit">追加する</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="copyright">@tabibookmarks</div>
    </footer>
    <!-- https://harekaze.jp/entry/45 からコピペ -->
    <script type="text/javascript">
        jQuery(function($) {
            //ページを読み込んだら、チェックボックスにクッキーの値を反映する
            if ($.cookie("checkbox-002_selected_value")) {

                var load_values = $.cookie("checkbox-002_selected_value").split(",");

                for (var i = 0; i < load_values.length; ++i) {
                    load_values[i] = decodeURIComponent(load_values[i]);
                }

                $("input[type=checkbox][name=checkbox-002]").each(function() {
                    this.checked = $.inArray(this.value, load_values) != -1;
                });
            }

            //チェックを変えたらクッキーを保存するイベントを登録する
            $("input[type=checkbox][name=checkbox-002]").change(function() {

                var save_values = [];

                $("input[type=checkbox][name=checkbox-002]").each(function() {
                    this.checked && save_values.push(encodeURIComponent(this.value));
                });

                $.cookie("checkbox-002_selected_value", save_values.join(","));
            });

            //▼現在のcookieの値を見る
            $("#btn_show_cookie").click(function() {
                $("#txt_cookie_value").val($.cookie("checkbox-002_selected_value"));
            });
        });
    </script>
</body>

</html>