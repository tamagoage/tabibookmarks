<?php
require('functions.php');

// セッションの期限を変更する
ini_set('session.gc_maxlifetime', 3600); // セッションが自動的に破棄されるまでの秒数を1時間に設定
ini_set('session.cookie_lifetime', 3600); // ブラウザがセッションクッキーを保持する期間を1時間に設定
// セッションを開始する
session_start();

if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="urlcreated.css">
    <title>URL作成ページ</title>
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <h1>LOGO</h1>
        </div>
    </header>
    <main class="main">
        <p class="pleasetap">下のエリアをタップ</p>
        <a href="shioripage/schedule.html">
            <div class="shiori">
                <div class="title-and-subtitle">
                    <div class="title">
                        <h1><?php echo h($form['title']); ?></h1>
                    </div>
                    <div class="subtitle"><?php echo h($form['subtitle']); ?></div>
                </div>
                <div class="date">
                    <h2><?php echo h($form['s_date']); ?>~<?php echo h($form['e_date']); ?></h2>
                </div>
                <button>copy!</button>
            </div>
        </a>
    </main>
    <footer class="footer">
        <div class="copyright">@tabibookmarks</div>
    </footer>
</body>

</html>