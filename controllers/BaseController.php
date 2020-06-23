<?php
/**
 * @author lichuang
 * @date   2018-11-21
 */

namespace app\controllers;


use Yii;
use yii\web\Controller;
use app\logic\BaseLogic;
use app\server\Api;

class BaseController extends Controller
{
    protected $baseLogic = null;
    protected $userId = 0;

    public function init()
    {
        if ($this->baseLogic == null) {
            $this->baseLogic = new BaseLogic();
        }

        // 请求日志
        $this->baseLogic->requestLogLogic();

        // 签名验证
        # $this->baseLogic->checkSignLogic();

        // 登录验证
        $userId = $this->baseLogic->isLoginLogic();

        if (floor($userId) > 0) {
            $this->userId = $userId;
        } else {
            Api::err(80000);
        }
    }

}
