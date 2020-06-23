<?php
/**
 * @author lichuang
 * @date   2018-12-11
 */

namespace app\logic;

use Yii;
use app\server\Helper;
use app\server\Log;
use app\server\Auth;
use app\server\Api;

class BaseLogic
{
    private $common  = null;

    public function __construct()
    {
        if ($this->common == null) {
            $this->common = Yii::$app->common;
        }
    }

    // 请求日志
    public function requestLogLogic()
    {
        $info = array();
        $info['route'] = Api::getRoute();
        $info['time'] = microtime(true);
        $info['ip'] = Helper::getIp();
        $info['auth'] = isset($_SERVER['HTTP_AUTH']) ? $_SERVER['HTTP_AUTH'] : '';
        $info['post'] = $_POST;

        // 记录请求日志
        if($this->common['apiHttp']) {
            $collectionName = "API_HTTP_".date("Y_m_d_H");
            Log::insert($collectionName, $info);
        }

    }

    // 登录验证
    public function isLoginLogic()
    {
        $auth = isset($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN'] : '';
        $userId = Auth::deAuth($auth);
        if (!$userId) {
            Api::err(60000);
        }
        return $userId;
    }

    // 签名验证
    public function checkSignLogic()
    {
        $post = $_POST;
        $checkSign = Auth::checkSign($post);
        if(!$checkSign){
            Api::err(70000);
        }
    }

}
