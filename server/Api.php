<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;
use app\server\Log;
use app\server\Server;

class Api extends Server
{
    // 输出数据
    static public function json($data)
    {
        $status = array();
        $status['code'] = 1;
        $status['msg'] = 'SUCCESS';

        $response = array();
        $response['route'] = self::getRoute();
        $response['time'] = microtime(true);
        $response['status'] = $status;

        $response['data'] = $data;

        // 记录输出日志
        $common = parent::getCommon();
        if ($common['apiEcho']) {
            $collectionName = "API_JSON_" . date("Y_m_d_H");
            Log::insert($collectionName, $response);
        }

        echo json_encode($response);
        exit;
    }

    // 输出错误
    static public function err($code, $msg = '')
    {
        $status = array();
        $status['code'] = $code;

        $err = parent::getErr();
        if (isset($err[$code]) && isset($err[$code])) {
            $status['msg'] = $msg . $err[$code];
        } else {
            $status['msg'] = $msg;
        }

        $response = array();
        $response['route'] = self::getRoute();
        $response['time'] = microtime(true);
        $response['status'] = $status;

        // 记录错误日志
        $common = parent::getCommon();
        if ($common['apiEcho']) {
            $collectionName = "API_ERR_" . date("Y_m_d_H");
            Log::insert($collectionName, $response);
        }

        echo json_encode($response);
        exit;
    }

    // 获取请求路由
    static public function getRoute()
    {
        $SERVER_NAME = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $SERVER_PORT = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
        $REQUEST_URI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if ($SERVER_PORT == '80' || empty($SERVER_PORT)) {
            $route = $SERVER_NAME . $REQUEST_URI;
        } else {
            $route = $SERVER_NAME . ':' . $SERVER_PORT . $REQUEST_URI;
        }
        return $route;
    }

}
