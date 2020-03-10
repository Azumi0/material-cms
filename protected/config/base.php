<?php
$env = getenv('APPLICATION_ENV');

defined('ADMIN_DIR') or define('ADMIN_DIR', 'gwadminpanel');
$admin_dir = ADMIN_DIR;

defined('MAIN_PATH') or define('MAIN_PATH', __DIR__ . '/../..');
defined('PROTECTED_PATH') or define('PROTECTED_PATH', __DIR__ . '/..');
defined('NEWS_TEMPLATES_PATH') or define('NEWS_TEMPLATES_PATH', PROTECTED_PATH . '/views/news_templates');

require(PROTECTED_PATH . '/materialhelpers/lib/ModulesAutoloader.php');

$paramsFileName = '/params.php';
$mailCfgFileName = '/mail.php';
$dbCfgFileName = '/db.php';

if ($env !== false) {
    if (is_readable(__DIR__ . "/params_$env.php")) {
        $paramsFileName = "/params_$env.php";
    }

    if (is_readable(__DIR__ . "/mail_$env.php")) {
        $mailCfgFileName = "/mail_$env.php";
    }

    if (is_readable(__DIR__ . "/db_$env.php")) {
        $dbCfgFileName = "/db_$env.php";
    }
}

$params = require(__DIR__ . $paramsFileName);
$mailCfg = require(__DIR__ . $mailCfgFileName);
$dbCfg = require(__DIR__ . $dbCfgFileName);

$pms = ModulesAutoloader::get();

if (preg_match('/' . $admin_dir . '/', $_SERVER['REQUEST_URI'])) {
    $defaultRoute = 'backend';

    $rules = array_merge(
        [
            '' => 'frontend/index',
            $admin_dir => 'backend/index',
            $admin_dir . '/system/sidebar/save' => 'backend/sidebar',
            $admin_dir . '/403' => 'backend/403',
            $admin_dir . '/lang/<lang:\w+>' => 'backend/lang'
        ],
        $pms['backend_routes'],
        [
            $admin_dir . '/sub/<module:\w+>/<submodule:\w+>/<action:\w+>/<id:([0-9]+)>' => '<module>/<submodule>/backend/<action>',
            $admin_dir . '/sub/<module:\w+>/<submodule:\w+>/<action:\w+>' => '<module>/<submodule>/backend/<action>',
            $admin_dir . '/sub/<module:\w+>/<submodule:\w+>' => '<module>/<submodule>/backend/index',
            $admin_dir . '/<module:\w+>/<action:\w+>/<id:([0-9]+)>' => '<module>/backend/<action>',
            $admin_dir . '/<module:\w+>/<action:\w+>' => '<module>/backend/<action>',
            $admin_dir . '/<module:\w+>' => '<module>/backend/index'
        ],
        $pms['routes']
    );

    $rules['<dt:(.*?)>'] = 'frontend/resolver';

    $identityClass = 'app\components\BackendIdentity';
    $enableSession = true;

    $basePath = dirname(__DIR__);

    $twigPathsBase = $basePath;
    $themeSettings = null;
} else {
    $defaultRoute = 'frontend';

    $rules = array_merge([
        '' => 'frontend/index'
    ], $pms['routes']);
    $rules['file/<dt:(.*?)>'] = 'frontend/file';

    $identityClass = 'app\components\FrontendIdentity';
    $enableSession = false;

    $basePath = dirname(__DIR__);
    $theme = 'default';
    $twigPathsBase = MAIN_PATH . '/themes/' . $theme;

    $themeSettings = [
        'basePath' => $twigPathsBase,
        'baseUrl' => $twigPathsBase,
        'pathMap' => [
            '@app/views' => $twigPathsBase . '/views',
            '@app/modules' => $twigPathsBase . '/modules'
        ],
    ];
}


$config = [
    'id' => 'materialcms',
    'basePath' => $basePath,
    'bootstrap' => ['log'],
    'defaultRoute' => $defaultRoute,
    'layout' => false,
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => $rules,
        ],
        'request' => [
            'cookieValidationKey' => 'uxoRv5AIntOqjuLVhX7qotjIDQqBitIn',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => $identityClass,
            'enableSession' => $enableSession,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => $defaultRoute . '/error',
        ],
        'mailer' => [
            'class'            => 'zyx\phpmailer\Mailer',
            'viewPath'         => $twigPathsBase . '/views/' . $defaultRoute,
            'useFileTransport' => false,
            'config'           => $mailCfg,
            'messageConfig'    => [
                'from' => [MAIL_FROM => MAIL_FROM_NAME]
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $dbCfg,
        'i18n' => [
            'translations' => [
                'yii' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/messages'
                ],
            ],
        ],
        'view' => [
            'class' => 'yii\web\View',
            'theme' => $themeSettings,
            'renderers' => [
                'twig' => [
                    'class' => 'materialhelpers\ViewRenderer',
                    'cachePath' => '@runtime/Twig/cache',
                    'paths' => [
                        $twigPathsBase . '/views/' . $defaultRoute,
                        $twigPathsBase . '/views/layouts/' . $defaultRoute
                    ],
                    'options' => [
                        'auto_reload' => true,
                        'autoescape' => 'html',
                    ],
                    'globals' => [
                        'html'  => ['class' => '\yii\helpers\Html'],
                        'ActiveForm' => ['class' => '\yii\widgets\ActiveForm'],
                        'Convert' => ['class' => 'materialhelpers\Convert'],
                        'Stat' => ['class' => 'materialhelpers\Stat'],
                        'System' => ['class' => 'materialhelpers\System'],
                        'MenuGenerator' => ['class' => 'materialhelpers\MenuGenerator'],
                        'MenuWidget' => ['class' => 'app\components\MenuWidget'],
                        'PagerWidget' => ['class' => 'app\components\PagerWidget']
                    ],
                    'functions' => [
                        'dump' => 'var_dump',
                        'explode' => 'explode',
                        'exists' => 'file_exists',
                        'jencode' => 'json_encode',
                        'jdecode' => 'json_decode',
                        'sizeof' => 'sizeof',
                        'implode' => 'implode',
                        'is_array' => 'is_array',
                        'combine' => 'array_combine',
                        'yt' => 'Yii::t',
                        'urld' => 'urldecode',
                        'urle' => 'urlencode',
                        'arr_key' => 'array_key_exists',
                        'nl2br' => 'nl2br',
                        'rand' => 'rand',
                        'gettype' => 'gettype'
                    ]
                ],
            ],
            'defaultExtension' => 'twig'
        ],
    ],
    'modules' => $pms['module'],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
}

/*var_dump($config['components']['urlManager']['rules']);
die();*/

return $config;
