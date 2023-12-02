# adc-character-code-detector

For metaps Advent Calendar 2023

このプロジェクトでは、SJIS, SJIS-win(Windows-31J, CP932, MS932), UTF-8の
テキストファイルから文字列を取り出して、その符号を出力させたり、
UTF-8の場合、Unicode上のコードポイントやUTF-8上の符号を出力します。
また、SJIS,SJIS-winの場合、2バイト文字を大雑把に分類して結果をターミナルに出力します。

## 使い方

```
cd adc-character-code-detector
```

### SJIS-winを指定した場合
detect_targets/w31j.txtに入力されている文字を1つずつチェックして
1バイト文字か、2バイト文字か判定し、符号をターミナルに出力します。
2バイト文字の場合、全角ひらがななのかIBM拡張文字なのか等大雑把な分類を行い
分類した結果をターミナルに出力します。
```
php index.php SJIS-win
```

### SJISを指定した場合
detect_targets/sjis.txtに入力されている文字を1つずつチェックして
1バイト文字か、2バイト文字か判定し、符号をターミナルに出力します。
2バイト文字の場合、大雑把な分類を行い結果をターミナルに出力します。
```
php index.php SJIS
```

### UTF-8を指定した場合
detect_targets/utf8.txtに入力されている文字を1つずつチェックして
Unicode上のコードポイント及び、UTF-8上の符号をターミナルに
出力します。
```
php index.php UTF-8
```