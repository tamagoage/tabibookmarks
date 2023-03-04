<?php
session_start();
require('functions.php');

// 配列の初期化
$form = [
    'title' => '',
    'subtitle' => '',
    's_date' => '',
    'e_date' => ''
];
$error = [];

// フォームに値が渡っているときに実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームの値を受け取る
    $form['title'] = filter_input(INPUT_POST, 'title', FILTER_DEFAULT);
    if ($form['title'] === "") {
        $error['title'] = 'blank';
    }

    $form['subtitle'] = filter_input(INPUT_POST, 'subtitle', FILTER_DEFAULT);

    // 正規表現
    $form['s_date'] = filter_input(INPUT_POST, 's_date', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/')));
    $form['e_date'] = filter_input(INPUT_POST, 'e_date', FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/')));

    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
    if ($form['password'] === "") {
        $error['password'] = 'blank';
    } else if (strlen($form['password']) < 6) {
        $error['password'] = 'length';
    }

    if(empty($error)) {
        $_SESSION['form'] = $form;
        header('Location:urlcreated.php');
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
    <link rel="stylesheet" href="common.css">
    <title>しおり作成ページ</title>
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <h1>LOGO</h1>
        </div>
    </header>
    <main class="main">
        <div class="shiori">
            <form action="" method="post">
                <dl>
                    <dt>Title</dt>
                    <dd><input type="text" name="title" value="<?php echo h($form['title']); ?>"></dd>
                    <?php if (isset($error['title']) && $error['title'] === 'blank') : ?>
                        <p class="error">* タイトルを入力してください</p>
                    <?php endif; ?>
                    <dt>Subtitle</dt>
                    <dd><input type="text" name="subtitle" value="<?php echo h($form['subtitle']); ?>"></dd>
                    <dt>Date</dt>
                    <dd><input type="date" name="s_date" value="<?php echo h($form['s_date']); ?>">～<input type="date" name="e_date" value="<?php echo h($form['e_date']); ?>"></dd>
                    <dt>Password</dt>
                    <dd><input type="password" name="password" value=""></dd>
                    <?php if (isset($error['password']) && $error['password'] === 'blank') : ?>
                        <p class="error">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
                        <p class="error">* ６文字以上で入力してください</p>
                    <?php endif; ?>
                </dl>

                <button type="submit">しおりを作る</button>
            </form>
        </div>
    </main>
    <footer class="footer">
        <div class="copyright">@tabibookmarks</div>
    </footer>
</body>

</html>