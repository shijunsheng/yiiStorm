<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;

class Aes
{
    const METHOD = 'AES-128-CBC';
    const KEY = 'Aes-62300.@';

    // 加密
    static public function encode($data)
    {
        $method = self::METHOD;
        $common = Yii::$app->common;
        $key = isset($common['aesKey']) ? $common['aesKey'] : self::KEY;
        $key = substr(sha1($key), 7, openssl_cipher_iv_length($method));
        $iv = $key;
        return openssl_encrypt($data, $method, $key, 0, $iv);
    }

    // 解密
    static public function decode($data)
    {
        $method = self::METHOD;
        $common = Yii::$app->common;
        $key = isset($common['aesKey']) ? $common['aesKey'] : self::KEY;
        $key = substr(sha1($key), 7, openssl_cipher_iv_length($method));
        $iv = $key;
        return openssl_decrypt($data, $method, $key, 0, $iv);
    }

}
