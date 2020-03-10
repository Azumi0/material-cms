<?php

//phpinfo(); die();
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');

$yiiLoader = __DIR__ . '/../protected/vendor/autoload.php';
$yiiCore = __DIR__ . '/../protected/vendor/yiisoft/yii2/Yii.php';
$yiiConfig = __DIR__ . '/../protected/config/base.php';

require($yiiLoader);
require($yiiCore);

$config = require($yiiConfig);

(new yii\web\Application($config))->run();
