## 简介

`yiiStorm` 是用于快速开发API的高性能MVC骨架   
基于`yii2`实现 剔除大量非常用扩展及代码    
在此基础上封装了API开发必备功能块 以极简的模式快速开发API  

https://gitee.com/one991/yiiStorm

## 目录结构
 
-  `commands` ：cli脚本
-  `config`   ：配置文件
-  `controllers` ：控制器层 
-  `logic` ：逻辑层
-  `models` ：数据层
-  `server` ：服务层
-  `vendor` ：三方库
-  `web` ：入口


<img src="https://one991.gitee.io/img/yiiStorm.png" />

## Nginx 配置

```bash
server {

    listen 80;
    server_name  192.168.1.200
    
    # 根目录为 web
    root /usr/local/nginx/www/api/web;

    location ~ \.php {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        fastcgi_param PATH_INFO $document_root$fastcgi_path_info;
    }
    
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

}
``` 

## 数据库配置

> MySQL 配置   
> 配置文件 `config/db.php`

> Redis 配置   
> 配置文件 `config/redis.php`

> MongoDB 配置   
> 配置文件 `config/mongo.php`

> RabbitMQ 配置   
> 配置文件 `config/amqp.php`

## 公共配置

> 公共配置文件 `config/common.php`

```php
<?php
// common.php
return
[
    // 框架默认配置项请勿删除
    // ...
    
    // 可自行添加所需配置项
    'oss' => 'https://one991.gitee.io/',
];


// 获取配置信息
$common = Yii::$app->common;
$common['oss'];

```

## 路由配置

> 路由配置文件 `config/rules.php`   

```php 
// rules.php      
<?php
return
[
   // 路由1
   '/demo1' => 'demo/user',
   // 路由2
   '/demo2' => 'demo/user-info',
];

// DemoController.php
<?php
class DemoController extends BaseController
{
    // 路由1 请求 DemoController 里面的 actionUser 方法
    public function actionUser()
    {
    }
    // 路由1 请求 DemoController 里面的 actionUserInfo 方法
    public function actionUserInfo()
    {
    }
}
```

## 控制器 Controller

> Controller 目录  `controller`

`controller/BaseController` 为控制器基类所有控制器需要继承它

```php   
class MemberController extends BaseController
{
    public function init()
    {
        // 执行父类构造方法
        // 父类构造方法中会对本次请求做以下处理
        // 签名验证/登录验证/记录日志/获取UID
        parent::init();
    }
}

// 如果不做登录验证处理则参考以下代码
class UserController extends BaseController
{
    // 逻辑层基类
    // BaseController 内部也是通过调用 baseLogic 内的验证方法实现的
    // 这里直接实例 baseLogic 选择性调用验证
    private $baseLogic = null;

    public function init()
    {
        if ($this->baseLogic == null) {
            $this->baseLogic = new BaseLogic();
        }
        // 请求日志
        $this->baseLogic->requestLogLogic();
        // 签名验证
        $this->baseLogic->checkSignLogic();
    }
}  
```


## 逻辑层 logic     

> Logic 目录  `logic`     

`logic/BaseLogic` 为逻辑层基类所有逻辑层需要继承它       

```php 
class MemberLogic extends BaseLogic
{
    public function __construct()
    {
        // 执行父类构造方法
        // 可将统一的逻辑处理封装到 BaseLogic 中
        parent::__construct();
    }
}
```


## 数据层 Model     

> Model 目录  `Models`     

`login/BaseModel` 为数据层基类所有数据层需要继承它       

```php 
class MemberModel extends BaseModel
{
    public function getDb()
    {
        // 通过获取基类的 $mysql 属性获取 mysql 实例
        return parent::$mysql
    }
}
```

## 业务流程

将 Controller 、 logic 、 Model 串起来

```php
// Controller - MemberController
class MemberController extends BaseController
{
    // 定义本控制器的逻辑层
    private $memberLogic = null;

    public function init()
    {
        parent::init();
        if ($this->memberLogic == null) {
            $this->memberLogic = new MemberLogic();
        }
    }
}

// logic - MemberLogic
class MemberLogic extends BaseLogic
{
    // 定义本逻辑的数据层
    private $memberModel = null;

    public function __construct()
    {
        parent::__construct();
        if ($this->memberModel == null) {
            $this->memberModel = new MemberModel();
        }
    }
}

// Model - MemberModel
class MemberModel extends BaseModel
{
    // 执行SQL
    public function userInfo($userId)
    {
        $sql = 'SELECT * FROM opt_user WHERE id = :id';
        $res = parent::$mysql->createCommand($sql);
        $res->bindValue(':id', $userId);
        $data = $res->queryOne();
        return $data;
    }
}    
```

## 输出

##### 正确输出
```php
$data = array('userId' => 100, 'age' => 30);
Api::json($data);

// 输出结果
{
    "route":"127.0.0.2/user/login",
    "time":1554614522.5488,
    "status":{
        "code":1,
        "msg":"SUCCESS"
    },
    "data":{
        "userId":"100",
        "age":"30"
    }
}

route  : 接口地址
time   : 请求时间
status : 请求结果
         {
            code ： 状态码 1请求成功  非1则为请求错误
                          ( 这里的成功表示 请求的参数和处理没出现任何问题 )
                          ( 这里的失败标识 请求参数或请求处理存在错误 )
            
            msg  :  错误信息 当 code 为 1 时 msg 始终为 SUCCESS 
                            当 code 非1 时 msg 为错误信息               
         }   
data  : 返回的数据 当 code 非1 时 可能会不存在此字段
```

##### 错误输出
```php
// 额外追加的错误信息 会追加到错误信息尾部 ( 一般不传 )
Api::err(60000, '额外追加的错误信息 可选');

// 输出结果
{
    "route":"127.0.0.2/user/login",
    "time":1553652462.9256,
    "status":{
        "code":60000,
        "msg":"请重新登录 额外追加的错误信息 可选"
    }
}

// 统一定义错误信息通过   config/err.php
// err.php
return
[
    '60000' => '请重新登录',
    '70000' => '签名错误'
]
```

## 登录
```php
// 只需将用户 id 传给 enAuth 方法自动生成 token
$token = Auth::enAuth($userId);

// 登录验证
// 前端每次请求接口将 token 以请求头形式发送到后端即可
// 请求头键名为: token 

// 在控制器基类 ( BaseController ) 中会对 token 进行验证
// 如果继承 BaseController 又不想进行登录验证 具体查看控制器文档
```

## 单点登录

> 默认为单点登录 也就是同一个账号同时只能登录一个客户端    
> 如果一个新的登录 那么会顶掉之前的登录

> 可以通过 `config/common.php` 配置允许的同时登录数量    

```php  
// 允许同一个用户同时在线数量 ( 如果同时登录超过此值则将之前的登录用户随机踢掉一个 )
'shareLogin' => 1,
```

## 退出登录
```php
// 只需将用户 token 传给 unAuth 方法即可
Auth::unAuth($token);
```

## 日志

> `config/common.php` 有2个日志配置项   
> 默认全为关闭 ( 开启须安装mongoDB扩展 )

```php
// 是否开启http请求日志 ( 开启后所有api接口请求信息都会写入到 MongoDB )
'apiHttp' => false,

// 是否开启输出日志 ( 开启后所有api接口输出信息都会写入到 MongoDB )
'apiEcho' => false,
```

> 上面2个日志会记录下 所有API请求信息 和 输出返回的信息情况   
> 如果需要写入一些自定义日志可以使用下面方法    
```php
// 写入自定义日志
$typeName = 'pay';
$logInfo = '余额不足';
Log::insert($typeName, $logInfo);
```

## 加密

> AES 加密   

```php    
// 加密
$str = Aes::encode('abc');

// 解密
Aes::decode($str);

`密钥在 config/common.php 配置`
```

## MySQL 操作

> MySQL 配置   
> 配置文件 `config/db.php`

```php

// Model 继承 BaseModel 基类
class MemberModel extends BaseModel
{
    // 单条查询
    public function userInfo($userId)
    {
        $sql = 'SELECT * FROM opt_user WHERE id = :id AND delete_flg = 0';
        $res = parent::$mysql->createCommand($sql);
        $res->bindValue(':id', $userId);
        $data = $res->queryOne();
        return $data;
    }
    
    // 多条查询
    public function userList($age)
    {
        $sql = 'SELECT * FROM opt_user WHERE age > :age';
        $res = parent::$mysql->createCommand($sql);
        $res->bindValue(':age', $age);
        $data = $res->queryAll();
        return $data;
    }
    
    // 非查询语句
    public function addUser($user, $password)
    {
        $sql = 'INSERT INTO opt_user(`user`,`password`)VALUES(:user,:password)';
        $res = parent::$mysql->createCommand($sql);
        $res->bindValue(':user', $user);
        $res->bindValue(':password', $password);
        // 返回影响行数
        return $res->execute();
    }
    
    // 事务
    public function register($user, $password)
    {
        $transaction = parent::$mysql->beginTransaction();
        $sql = array();
        $i = 0;
        try {

            $sql['user'] = 'INSERT INTO opt_user(`user`,`password`)VALUES(:user,:password)';
            $res = parent::$mysql->createCommand($sql['user']);
            $res->bindValue(':user', $user);
            $res->bindValue(':password', $password);
            // 返回影响行数
            $i += $res->execute();
            
            // 新用户 Id
            $userId = parent::$mysql->getLastInsertID();

            $sql['balance'] = 'INSERT INTO opt_balance(`user_id`,`available_num`)
                               VALUES(:user_id,:available_num)';

            $res = parent::$mysql->createCommand($sql['balance']);
            $res->bindValue(':user_id', $userId);
            $res->bindValue(':available_num', 0);
            $i += $res->execute();
            
            // 提交事务
            $transaction->commit();

            if($i == count($sql)){
                return $i;
            }else{
                $transaction->rollBack();
                return false;
            }

        } catch(\Exception $e) {
            $transaction->rollBack();
            return false;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            return false;
        }

    }

}
```

## RPC
```php
// Server 
Rpc::server($obj);

// Client
$url = 'http://192.168.2.250/rpc';
$client = Rpc::client($url);
```

# cli 脚本

> `commands` 下为 cli 脚本文件

```php
class TaskController extends BaseController
{
    // 任务1
    public function actionIndex()
    {
    }
    // 任务2
    public function actionGetIndex()
    {
    }
}

// 执行上面的两个任务使用命令
php yii task/index
php yii task/get-index

( yii 在框架根目录下 )

```









         
        

