<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;

class Server
{
    static private $err = null;
    static private $common  = null;

    public function __construct()
    {

    }

    // 错误信息
    static public function getErr()
    {
        if (self::$err == null) {
            self::$err = Yii::$app->err;
        }
        return self::$err;
    }

    // 配置信息
    static public function getCommon()
    {
        if (self::$common == null) {
            self::$common = Yii::$app->common;
        }
        return self::$common;
    }

}
