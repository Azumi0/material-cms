<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\GAccessTokensTable;
use yii\console\Controller;


class TokenController extends Controller
{
    /**
     * This action removes expired tokens from DB
     */
    public function actionIndex()
    {
        $tokenDB = new GAccessTokensTable();
        $tokenDB->cleanExpired();

        echo PHP_EOL . 'OK' . PHP_EOL;
    }
}
