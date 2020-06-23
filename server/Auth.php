<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;
use app\server\Aes;
use app\server\Server;

class Auth extends Server
{
    const KEY = 'Key-62300.@';

    // 生成用户身份验证
    static public function enAuth($userId = 0)
    {
        if (floor($userId) == 0) {
            return false;
        }

        // 生成唯一token
        $token = sha1(microtime(true) . uniqid(true) . mt_rand(10000000, 99999999));

        // 获取公共配置
        $common = parent::getCommon();
        // 允许同一个用户同时在线数量
        $shareLogin = isset($common['shareLogin']) ? floor($common['shareLogin']) : 1;
        // 要存储的token的key名称
        $tokenKey = 'token_'.$userId;
        // 判断当前用户是否达到同时在线上限
        $isLoginNum = Yii::$app->redis->scard($tokenKey);
        if($isLoginNum >= $shareLogin){
            // 多余的用户数量 一般情况这个值都为1
            $surplus = $isLoginNum - $shareLogin;
            // 随机删除集合内 ( $surplus + 1 ) 个值
            Yii::$app->redis->spop($tokenKey, ($surplus + 1));
        }
        // token存入redis ( 以userId为键名 以token为值 存一个集合 )
        Yii::$app->redis->sadd($tokenKey, $token);

        // userId进行AES加密
        $userId = Aes::encode($userId);
        // 获取用户认证的密钥
        $key = isset($common['authKey']) ? $common['authKey'] : self::KEY;
        // 通过token和当前用户id和密钥生成身份密钥
        $auth = substr(sha1(sha1($key . $token . $userId)), 6, 30) . $userId;

        return $auth;
    }

    // 验证用户身份
    static public function deAuth($auth = '')
    {
        // 获取userId部分
        $enUserId = substr($auth, 30);
        $userId = floor(Aes::decode($enUserId));
        if (!$userId) {
            return false;
        }
        // token的key名称
        $tokenKey = 'token_'.$userId;
        // 获取用户当前所有token
        $tokenAll = Yii::$app->redis->smembers($tokenKey);
        // 通过查询到的token和解密后的userId生成签名
        $common = parent::getCommon();
        foreach ($tokenAll as $val){
            $token = $val;
            $key = isset($common['authKey']) ? $common['authKey'] : self::KEY;
            $newAuth = substr(sha1(sha1($key . $token . $enUserId)), 6, 30) . $enUserId;
            // 比对auth
            if ($auth === $newAuth) {
                return $userId;
            }
        }
        return false;
    }

    // 退出登录
    static public function unAuth($token)
    {

    }

    // 验证签名
    static public function checkSign($data)
    {
        $sign = isset($data['sign']) ? $data['sign'] : '';
        $makeSign = self::makeSign($data);
        if ($sign !== $makeSign) {
            return false;
        }
        return true;
    }

    // 生成签名
    static public function makeSign($data)
    {
        if (isset($data['sign'])) {
            unset($data['sign']);
        }
        ksort($data);
        $sign = '';
        foreach ($data as $k => $val) {
            if (!empty($val)) {
                $sign .= $k . '=' . $val . '&';
            }
        }
        $common = parent::getCommon();
        $key = isset($common['signKey']) ? $common['signKey'] : self::KEY;
        $sign = trim($sign, '&') . $key;
        return sha1($sign);
    }

}
