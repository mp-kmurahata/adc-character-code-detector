<?php

require_once('./helper.php');
require_once('./detect.php');

// コマンドライン引数のチェック
check_parameter($argv);

// 文字コードの検出処理
detect($argv[1]);


