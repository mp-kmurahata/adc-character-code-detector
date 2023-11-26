<?php

if (!function_exists('is_shift_jis_one_byte_char')) {
    /**
     * @param string $val
     * @return bool
     */
    function is_shift_jis_one_byte_char(string $val): bool
    {
        // 先頭の1バイト分（16進数の値2つ分 = 1バイト分）の文字コードを取得
        $tmp_hex = mb_substr(bin2hex($val), 0, 2);
        $tmp_0x_hex = '0x' . $tmp_hex;

        if (
            // 制御文字
            (intval($tmp_0x_hex, 16) >= 0x00 && intval($tmp_0x_hex, 16) <= 0x1F ) ||
            // SP（制御文字）
            intval($tmp_0x_hex, 16) === 0x20 ||
            // DEL（制御文字）
            intval($tmp_0x_hex, 16) === 0x7F ||
            // ラテン文字用図形文字（半角記号・半角数値・大文字小文字アルファベット）
            (intval($tmp_0x_hex, 16) >= 0x21 && intval($tmp_0x_hex, 16) <= 0x7E ) ||
            // 片仮名用図形文字 (いわゆる半角カナ)
            (intval($tmp_0x_hex, 16) >= 0xA1 && intval($tmp_0x_hex, 16) <= 0xDF ) ||
            // 予約
            intval($tmp_0x_hex, 16) === 0x80 ||
            // 予約
            intval($tmp_0x_hex, 16) === 0xA0 ||
            // 予約
            (intval($tmp_0x_hex, 16) >= 0xFD && intval($tmp_0x_hex, 16) <= 0xFF )
        ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_shift_jis_double_byte_char')) {
    /**
     * @param string $val
     * @return bool
     */
    function is_shift_jis_double_byte_char(string $val): bool
    {
        // 先頭の1バイト分（16進数の値2つ分 = 1バイト分）の文字コードを取得
        $tmp_hex = mb_substr(bin2hex($val), 0, 2);
        $tmp_0x_hex = '0x' . $tmp_hex;

        if (
            (intval($tmp_0x_hex, 16) >= 0x81 && intval($tmp_0x_hex, 16) <= 0x9f ) ||
            (intval($tmp_0x_hex, 16) >= 0xe0 && intval($tmp_0x_hex, 16) <= 0xfc )
        ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('categorize_double_byte_char')) {
    /**
     * for SJIS-win
     * NEC特殊文字, NEC選定IBM拡張文字, IBM拡張文字, 第一水準漢字, 第二水準漢字
     * 全角アルファベット, 全角ひらがな, 全角カタカナ, 全角数字, 全角記号 を大雑把に分類します（未定義領域も範囲特定に含めたりしてるので厳密ではありません。）
     * @param string $val
     */
    function categorize_double_byte_char(string $val): void
    {
        $tmp_hex = mb_substr(bin2hex($val), 0, 2);
        $tmp_0x_hex = '0x' . $tmp_hex;

        if (
            (intval($tmp_0x_hex, 16) >= 0x81 && intval($tmp_0x_hex, 16) <= 0x9f ) ||
            (intval($tmp_0x_hex, 16) >= 0xe0 && intval($tmp_0x_hex, 16) <= 0xfc )
        ) {
            $tmp_hex = bin2hex($val);
            $tmp_0x_hex = '0x' . $tmp_hex;

            // NEC特殊文字 (13区) | 丸付き数字 Tel, cm, kg等の1文字単位 1文字元号 ローマ数字
            // https://www2d.biglobe.ne.jp/~msyk/charcode/cp932/Windows-31J-charset.html
            if (
                // 丸付き数字と、ローマ数字
                (intval($tmp_0x_hex, 16) >= 0x8740 && intval($tmp_0x_hex, 16) <= 0x875D ) ||
                (intval($tmp_0x_hex, 16) >= 0x875F && intval($tmp_0x_hex, 16) <= 0x8775 ) ||
                intval($tmp_0x_hex, 16) == 0x877E ||
                (intval($tmp_0x_hex, 16) >= 0x8780 && intval($tmp_0x_hex, 16) <= 0x879C )
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、NEC特殊文字に分類されています' . PHP_EOL);
            }

            // NEC選定IBM拡張文字(89～92区) |
            if (
                (intval($tmp_0x_hex, 16) >= 0xED40 && intval($tmp_0x_hex, 16) <= 0xED7E ) ||
                (intval($tmp_0x_hex, 16) >= 0xED80 && intval($tmp_0x_hex, 16) <= 0xEDFC )
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、NEC選定IBM拡張文字に分類されています' . PHP_EOL);
            }

            // IBM拡張文字 (115～119区) |
            if (
                (intval($tmp_0x_hex, 16) >= 0xFA40 && intval($tmp_0x_hex, 16) <= 0xFA7E ) ||
                (intval($tmp_0x_hex, 16) >= 0xFA80 && intval($tmp_0x_hex, 16) <= 0xFAFC ) ||
                (intval($tmp_0x_hex, 16) >= 0xFB40 && intval($tmp_0x_hex, 16) <= 0xFB7E ) ||
                (intval($tmp_0x_hex, 16) >= 0xFB80 && intval($tmp_0x_hex, 16) <= 0xFBFC ) ||
                (intval($tmp_0x_hex, 16) >= 0xFC40 && intval($tmp_0x_hex, 16) <= 0xFC4B )
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、IBM拡張文字に分類されています' . PHP_EOL);
            }

            // 第一水準漢字（未定義領域もめんどくさいので少し含めてるので厳密ではない）
            // 0x889F(亜) ~ 0x9872(腕)
            if (intval($tmp_0x_hex, 16) >= 0x889F && intval($tmp_0x_hex, 16) <= 0x9872) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、第一水準漢字に分類されています' . PHP_EOL);
            }

            // 第二水準漢字（未定義領域もめんどくさいので少し含めてるので厳密ではない）
            // 0x989F(弌) ~ 0xEAA4(熙)
            if (intval($tmp_0x_hex, 16) >= 0x989F && intval($tmp_0x_hex, 16) <= 0xEAA4) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、第二水準漢字に分類されています' . PHP_EOL);
            }

            // 全角アルファベット
            if (
                // 全角大文字アルファベット
                (intval($tmp_0x_hex, 16) >= 0x8260 && intval($tmp_0x_hex, 16) <= 0x8279 ) ||
                // 全角小文字アルファベット
                (intval($tmp_0x_hex, 16) >= 0x8281 && intval($tmp_0x_hex, 16) <= 0x829A )
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、全角アルファベットに分類されています' . PHP_EOL);
            }

            // 全角ひらがな
            if (
                // 全角ひらがな
                (intval($tmp_0x_hex, 16) >= 0x82A0 && intval($tmp_0x_hex, 16) <= 0x82F1 )
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、全角ひらがなに分類されています' . PHP_EOL);
            }

            // 全角カタカナ
            if (
                // 全角カタカナ
                (intval($tmp_0x_hex, 16) >= 0x8340 && intval($tmp_0x_hex, 16) <= 0x8396)
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、全角カタカナに分類されています' . PHP_EOL);
            }

            // 全角数字
            if (
                // 全角数字
                (intval($tmp_0x_hex, 16) >= 0x824F && intval($tmp_0x_hex, 16) <= 0x8258)
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、全角数字に分類されています' . PHP_EOL);
            }

            // 全角記号 ※未定義も含めた厳密ではない範囲指定をしています。
            // 文字コード範囲は以下を参考にしています
            // https://seiai.ed.jp/sys/text/java/shiftjis_table.html
            if (
                // 全角記号
                (intval($tmp_0x_hex, 16) >= 0x8140 && intval($tmp_0x_hex, 16) <= 0x81FF)
            ) {
                echo( '「' . mb_convert_encoding($val, 'UTF-8', 'SJIS-win') . '」は、全角記号に分類されています' . PHP_EOL);
            }
        }
    }
}

if (!function_exists('detect')) {
    function detect(string $encoding): void
    {
        // ファイルから文字列を全て取り出す
        $text = get_text_from_file($encoding);
        // 1文字ずつ配列化
        $chars = mb_str_split($text, 1, $encoding);
        // echo(print_r($chars, true).PHP_EOL);

        // 1文字ずつ分類していく
        foreach ($chars as $char) {
            // HEXの値を取得
            $tmp_hex = bin2hex($char);
            // SJIS, SJIS-winの場合、文字は1バイトまたは2バイト文字のどちらか
            if ($encoding === 'SJIS-win' || $encoding === 'SJIS') {
                // 1バイト文字の場合
                if (is_shift_jis_one_byte_char($char)) {
                    // UTF-8にエンコードしてあげているのは、自環境のターミナルのエンコードがUTF-8の為
                    echo( '「' . mb_convert_encoding($char, 'UTF-8', $encoding) . '」は、1バイト文字です | 符号（文字コード） = ' . $tmp_hex . PHP_EOL);
                    continue;
                }
                // 2バイト文字の場合
                if (is_shift_jis_double_byte_char($char)) {
                    // UTF-8にエンコードしてあげているのは、自環境のターミナルのエンコードがUTF-8の為
                    echo( '「' . mb_convert_encoding($char, 'UTF-8', $encoding) . '」は、2バイト文字です | 符号（文字コード） = ' . $tmp_hex . PHP_EOL);
                    // 2バイト文字であれば、カテゴライズしてみる。
                    categorize_double_byte_char($char);
                    continue;
                }
            }

            // Unicode上のコードポイントと、UTF-8上の符号を出す ※SJIS-winのようにカテゴライズまではしない
            if ($encoding === 'UTF-8') {
                // Unicode上のコードポイントを取得
                $code_point = bin2hex(
                    // https://www.cresco-es.co.jp/special/news/407
                    // によれば、日本語はUCS-2の中に全て入っているみたいなので、UCS-4ではなくUCS-2にエンコードしています
                    mb_convert_encoding($char, 'UCS-2', 'UTF-8')
                );
                // UTF-8上の符号（文字コード）を取得
                $char_code = bin2hex($char);
                echo( '「' . $char . '」は、' . (strlen($char_code) / 2) . 'バイト文字です | Unicode(UCS-2)上の符号位置（コードポイント） = ' . $code_point . ' | UTF-8上の符号（文字コード） = ' . $char_code . PHP_EOL);
            }
        }
    }
}