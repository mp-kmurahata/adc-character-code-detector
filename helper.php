<?php

if (!function_exists('check_parameter')) {
    /**
     * @param array $argv
     */
    function check_parameter(array $argv): void
    {
        // コマンドライン引数が1つのみ指定されていれば処理継続
        if (count($argv) < 2) {
            echo('引数が指定されていません。UTF-8, SJIS, SJIS-win のいずれかを指定ください。');
            exit;
        } elseif (count($argv) > 2) {
            echo('引数は1つのみ指定可能です。');
            exit;
        }

        // コマンドライン引数が特定のパラメタ値であることを確認
        if (!in_array($argv[1], ['UTF-8', 'SJIS', 'SJIS-win'], true)) {
            echo('引数に指定できるパラメーターは、UTF-8, SJIS, SJIS-win のいずれかになります。' . PHP_EOL);
            exit;
        }
    }
}

if (!function_exists('get_text_from_file')) {
    /**
     * @param string $encoding
     * @return string
     */
    function get_text_from_file(string $encoding): string
    {
        $file_paths = [
            'SJIS'     => './detect_targets/sjis.txt',
            'UTF-8'    => './detect_targets/utf8.txt',
            'SJIS-win' => './detect_targets/w31j.txt'
        ];

        $file_handler = fopen($file_paths[$encoding], "r");
        // $line_number = 1;
        $text = '';

        while ($line = fgets($file_handler)) {
            // $line_number += 1;
            $text = $text . $line;
        }

        fclose($file_handler);

        return $text;
    }
}
