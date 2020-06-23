<?php
# 公共配置
return
[
    # AES加密密钥
    'aesKey'  => 'aesKey-62300.@',

    # 用户认证密钥
    'authKey' => 'authKey-62300.@',

    # 生成签名密钥
    'signKey' => 'signKey-62300.@',

    # 是否开启http请求日志 ( 开启后所有api接口请求信息都会写入到MongoDB )
    'apiHttp' => false,

    # 是否开启输出日志 ( 开启后所有api接口输出信息都会写入到MongoDB )
    'apiEcho' => false,

    # 允许同一个用户同时在线数量 ( 如果同时登录超过此值则将之前的登录用户随机踢掉一个 )
    'shareLogin' => 1,

];