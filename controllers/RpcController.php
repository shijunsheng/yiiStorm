<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\logic\BaseLogic;
use app\server\Rpc;

class RpcController extends Controller
{
    public function init()
    {

    }

    // RPC server
    public function actionApi()
    {
        Rpc::server(new BaseLogic());
    }

    // RPC client
    public function actionTest()
    {
        $url = 'http://192.168.2.250/rpc';
        $client = Rpc::client($url);
        $client->checkSignLogic();
    }

}
