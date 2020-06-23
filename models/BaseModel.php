<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{
    public static $mysql = null;

    public function init()
    {
        self::mysql();
    }

    public static function mysql()
    {
        if (self::$mysql == null) {
            self::$mysql = Yii::$app->db;
        }
    }

}
