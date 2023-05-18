<?php 
require('../functions.php');
require('../get_id.php');

$db = dbconnect();
// 画面に表示する処理
// urlパラメーターがtravels.urlと一致するものを取得
$stmt = $db->prepare('SELECT travels.id, places_id, name, address, googlemap_url FROM places 
                        JOIN travels ON places.travels_r_id = travels.id
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
$stmt->bind_result($id, $places_id, $name, $address, $googlemap_url);

// 結果セットをメモリに格納する
$stmt->store_result();

// travelテーブルのidと紐づけるために変数に代入
$travels_r_id = $id;

$places = [];

// すべての行を取得する
while ($stmt->fetch()) {

    // 取得した行を変数に代入
    $places[] = [
            'places_id' => $places_id,
            'name' => $name,
            'address' => $address,
            'googlemap_url' => $googlemap_url
        ];
}

// placesの削除
// delete関数用の変数用意
$places_table = 'places';
$places_id = 'places_id';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    delete($db, $places_table, $places_id, $delete_id);
    header('Location: places.php?id=' . $url);
    exit();
}

// placesの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
    if ($form['name'] === "") {
        $error['name'] = 'blank';
    }

    $form['address'] = filter_input(INPUT_POST, 'address', FILTER_DEFAULT);

    $form['googlemap_url'] = filter_input(INPUT_POST, 'googlemap_url', FILTER_DEFAULT);

    // dbに登録
    if (empty($error)) {
        $stmt_while = $db->prepare('INSERT INTO places (travels_r_id, name, address, googlemap_url) values(?,?,?,?)');
        if (!$stmt_while) {
            die($db->error);
        }

        $stmt_while->bind_param('isss', $travels_r_id, $form['name'], $form['address'], $form['googlemap_url']);
        $success = $stmt_while->execute();
        if (!$success) {
            die($db->error);
        }
        $stmt_while->close();

        header('Location: places.php?id=' . $url);
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
    <link rel="stylesheet" href="css/places.css">
    <title>行きたいところリスト</title>
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
                <h1>places</h1>
            </div>
            <div class="bookmarks-erea">
                <?php foreach ($places as $places_while): ?>
                <table>
                    <tbody>
                        <tr class="name">
                            <td colspan="7"><?php echo h($places_while['name']); ?></td>
                        </tr>
                        <tr class="googlemap">
                            <td colspan="7"><?php echo h($places_while['googlemap_url']); ?></td>
                        </tr>
                        <tr class="address">
                            <td colspan="7"><?php echo h($places_while['address']); ?></td>
                        </tr>
                    </tbody>
                </table>
                <!-- 削除フォーム -->
                <form action="" method="post">
                    <input type="hidden" name="delete_id" value="<?php echo h($places_while['places_id']); ?>">
                    <input type="submit" value="削除">
                </form>
                <?php endforeach; ?>
                <div class="open-area">
                    <label class="open" for="pop-up">✙</label>
                </div>
            </div>
            <input type="checkbox" id="pop-up">
            <div class="overlay">
                <div class="window">
                    <label class="close" for="pop-up">×</label>
                    <h3>?日目</h3>
                    <form action="" method="post">
                        <dl class="form-area">
                            <label for="name">
                                <dt>店名</dt>
                                <dd><input class="textarea" type="text" name="name"></dd>
                            </label>
                            <label for="address">
                                <dt>場所</dt>
                                <dd><input class="textarea" type="datetime" name="address"></dd>
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
        </div>
    </main>
    <footer class="footer">
        <div class="copyright">@tabibookmarks</div>
    </footer>
</body>
</html>