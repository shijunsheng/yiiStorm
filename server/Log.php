<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\server;

use Yii;
use app\server\Server;

class Log extends Server
{
    // 写入日志
    static public function insert($collectionName, $arr)
    {
        if(empty($collectionName) || empty($arr)){
            return false;
        }

        $collection = Yii::$app->mongodb->getCollection($collectionName);
        $res = $collection->insert($arr);
        return $res;
    }

}
