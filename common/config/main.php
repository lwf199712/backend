<?php declare(strict_types=1);

use common\components\WechatCache;
use common\components\Wechat;
use Da\QrCode\Component\QrCodeComponent;
use common\components\Logistics;
use common\components\UploadDrive;
use common\components\Pay;
use yii\queue\LogBehavior;
use yii\queue\redis\Queue;
use Detection\MobileDetect;
use common\components\Debris;
use yii\redis\Connection;
use services\Application;
use common\components\Init;
use yii\caching\FileCache;

return [
    'name' => 'RageFrameaa',
    'version' => '1.0',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'language' => 'zh-CN',
    'sourceLanguage' => 'zh-cn',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => [
        'queue', // 队列系统
        Init::class, // 加载默认的配置
    ],
    'components' => [
        /** ------ 缓存 ------ **/
        'cache' => [
            'class' => FileCache::class,
            /**
             * 文件缓存一定要有，不然有可能会导致缓存数据获取失败的情况
             *
             * 注意如果要改成非文件缓存请删除，否则会报错
             */
            'cachePath' => '@backend/runtime/cache'
        ],
        /** ------ 格式化时间 ------ **/
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],
        /** ------ 服务层 ------ **/
        'services' => [
            'class' => Application::class,
        ],
        /** ------ redis配置 ------ **/
        'redis' => [
            'class' => Connection::class,
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        /** ------ websocket redis配置 ------ **/
        'websocketRedis' => [
            'class' => Connection::class,
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 1,
        ],
        /** ------ 网站碎片管理 ------ **/
        'debris' => [
            'class' => Debris::class,
        ],
        /** ------ 访问设备信息 ------ **/
        'mobileDetect' => [
            'class' => MobileDetect::class,
        ],
        /** ------ 队列设置 ------ **/
        'queue' => [
            'class' => Queue::class,
            'redis' => 'redis', // 连接组件或它的配置
            'channel' => 'queue', // Queue channel key
            'as log' => LogBehavior::class,// 日志
        ],
        /** ------ 公用支付 ------ **/
        'pay' => [
            'class' => Pay::class,
        ],
        /** ------ 上传组件 ------ **/
        'uploadDrive' => [
            'class' => UploadDrive::class,
        ],
        /** ------ 快递查询 ------ **/
        'logistics' => [
            'class' => Logistics::class,
        ],
        /** ------ 二维码 ------ **/
        'qr' => [
            'class' => QrCodeComponent::class,
            // ... 您可以在这里配置组件的更多属性
        ],
        /** ------ 微信SDK ------ **/
        'wechat' => [
            'class' => Wechat::class,
            'userOptions' => [],  // 用户身份类参数
            'sessionParam' => 'wechatUser', // 微信用户信息将存储在会话在这个密钥
            'returnUrlParam' => '_wechatReturnUrl', // returnUrl 存储在会话中
            'rebinds' => [
                'cache' => WechatCache::class,
            ]
        ],
    ],
];
