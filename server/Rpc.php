<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;
use app\server\Server;

class Rpc extends Server
{
    // 服务端
    static public function server($object)
    {
        $service = new \Yar_Server($object);
        $service->handle();
        exit;
    }

    // 客户端
    static public function client($url)
    {
        $client = new \Yar_Client($url);
        return $client;
    }

}
