<?php

@mkdir('apks',  0777, true);   // 放 apk
@mkdir('icons', 0777, true);   // 放图标

// 如果有文件被提交，就处理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = @$_POST['name'] ?: '未命名应用';
    $apk  = @$_FILES['apk'];
    $icon = @$_FILES['icon'];

    if ($apk && $apk['error'] == 0) {
        $apkPath  = 'apks/'  . basename($apk['name']);
        move_uploaded_file($apk['tmp_name'], $apkPath);
    }
    if ($icon && $icon['error'] == 0) {
        $iconPath = 'icons/' . basename($icon['name']);
        move_uploaded_file($icon['tmp_name'], $iconPath);
    }

    // 追加 JSON
    $list = [];
    if (file_exists('apk_list.json')) {
        $list = json_decode(file_get_contents('apk_list.json'), true) ?: [];
    }
    $list[] = [
        'name' => $name,
        'icon' => 'http://'.$_SERVER['HTTP_HOST'].'/icons/'.basename($icon['name']),
        'apk'  => 'http://'.$_SERVER['HTTP_HOST'].'/apks/'.basename($apk['name'])
    ];
    file_put_contents('apk_list.json', json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo '<h3>上架完成！客户端刷新即可看到新应用</h3><hr>';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>逊龢岛-后端</title>
</head>
<body>
<h2>逊龢岛 - 上架</h2>
<form method="post" enctype="multipart/form-data">
应用名称：<input type="text" name="name" required><br>
选择APK：<input type="file" name="apk" accept=".apk" required><br>
选择图标：<input type="file" name="icon" accept="image/*" required><br>
<button type="submit">立即上架</button>
</form>
<hr>
<a href="apk_list.json" target="_blank">查看当前列表</a>
</body>
</html>
