<?php declare(strict_types=1);

use backend\widgets\notify\NotifyController;
use common\widgets\cropper\CropperController;
use common\widgets\selectmap\MapController;
use common\widgets\provinces\ProvincesController;
use common\widgets\ueditor\UeditorController;
use common\controllers\FileBaseController;
use yii\widgets\LinkPager;
use yii\web\Response;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;
use yii\log\FileTarget;
use common\models\backend\Member;
use backend\modules\common\Module;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'main', // 默认控制器
    'bootstrap' => ['log'],
    'modules' => [
        /** ------ 公用模块 ------ **/
        'common' => [
            'class' => Module::class,
        ],
        /** ------ 基础模块 ------ **/
        'base' => [
            'class' => \backend\modules\base\Module::class,
        ],
        /** ------ 会员模块 ------ **/
        'member' => [
            'class' => \backend\modules\member\Module::class,
        ],
        /** ------ oauth2 ------ **/
        'oauth2' => [
            'class' => \backend\modules\oauth2\Module::class,
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => Member::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'idParam' => '__backend',
            'on afterLogin' => static function ($event) {
                Yii::$app->services->backendMember->lastLogin($event->identity);
            },
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
            'timeout' => 86400,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/' . date('Y-m/d') . '.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'assetManager' => [
            // 'linkAssets' => true,
            'bundles' => [
                JqueryAsset::class => [
                    'js' => [],
                    'sourcePath' => null,
                ],
                BootstrapAsset::class => [
                    'css' => [],  // 去除 bootstrap.css
                    'sourcePath' => null,
                ],
                BootstrapPluginAsset::class => [
                    'js' => [],  // 去除 bootstrap.js
                    'sourcePath' => null,
                ],
            ],
        ],
        'response' => [
            'class' => Response::class,
            'on beforeSend' => function ($event) {
                Yii::$app->services->log->record($event->sender);
            },
        ],
    ],
    'container' => [
        'definitions' => [
            LinkPager::class => [
                'nextPageLabel' => '<i class="icon ion-ios-arrow-right"></i>',
                'prevPageLabel' => '<i class="icon ion-ios-arrow-left"></i>',
                'lastPageLabel' => '<i class="icon ion-ios-arrow-right"></i><i class="icon ion-ios-arrow-right"></i>',
                'firstPageLabel' => '<i class="icon ion-ios-arrow-left"></i><i class="icon ion-ios-arrow-left"></i>',
            ]
        ],
        'singletons' => [
            // 依赖注入容器单例配置
        ]
    ],
    'controllerMap' => [
        'file' => FileBaseController::class, // 文件上传公共控制器
        'ueditor' => UeditorController::class, // 百度编辑器
        'provinces' => ProvincesController::class, // 省市区
        'select-map' => MapController::class, // 经纬度选择
        'cropper' => CropperController::class, // 图片裁剪
        'notify' => NotifyController::class, // 消息
    ],
    'params' => $params,
];
