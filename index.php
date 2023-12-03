<?php

require_once('./helper.php');
require_once('./detect.php');

// コマンドライン引数のチェック
check_parameter($argv);

// 符号の出力処理
detect($argv[1]);


