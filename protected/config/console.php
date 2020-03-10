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
$otherCfgFileName = '/other.php';

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

    if (is_readable(__DIR__ . "/other_$env.php")) {
        $otherCfgFileName = "/other_$env.php";
    }
}

$params = require(__DIR__ . $paramsFileName);
$mailCfg = require(__DIR__ . $mailCfgFileName);
$dbCfg = require(__DIR__ . $dbCfgFileName);
$otherCfg = require(__DIR__ . $otherCfgFileName);

$pms = ModulesAutoloader::get();

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

$basePath = dirname(__DIR__);

$twigPathsBase = $basePath;
$themeSettings = null;

$config = [
    'id' => 'console',
    'basePath' => $basePath,
    'bootstrap' => ['log'],
    'defaultRoute' => $defaultRoute,
    'controllerNamespace' => 'app\commands',
    'components' => [
        'urlManager' => [
            'baseUrl' => $otherCfg['baseUrl'],
            'scriptUrl' => $otherCfg['baseUrl'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => $rules
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
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $dbCfg,
    ],
    'params' => $params,
];

return $config;
