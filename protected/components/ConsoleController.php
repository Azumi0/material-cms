<?php

namespace app\components;

use Yii;
use yii\helpers\Console;
use yii\console\Controller AS YiiController;

class ConsoleController extends YiiController
{
    public function beforeAction($action) {
        parent::beforeAction($action);

        defined('default_lang') or define('default_lang', 'pl');

        Yii::$app->language = default_lang;

        return true;
    }

    public function getParam($name, $default = false)
    {
        return Yii::$app->request->getQueryParam($name, $default);
    }

    public function getGet($name, $default = false)
    {
        return Yii::$app->get($name, $default);
    }

    public function getPost($name = null, $default = false)
    {
        return Yii::$app->request->post($name, $default);
    }

    public function getReferrer()
    {
        return Yii::$app->request->referrer;
    }

    public function isPost()
    {
        return Yii::$app->request->isPost;
    }

    public function isGet()
    {
        return Yii::$app->request->isGet;
    }

    public function isPut()
    {
        return Yii::$app->request->isPut;
    }

    public function isDelete()
    {
        return Yii::$app->request->isDelete;
    }

    public function isAjax()
    {
        return Yii::$app->request->isAjax;
    }

    public function getRequestType()
    {
        return ($this->isPost()) ? 'post' : (($this->isGet()) ? 'get' : (($this->isPut()) ? 'put' : (($this->isDelete()) ? 'delete' : 'unknown')));
    }

    public function url($name, $params = [], $absolute = false)
    {
        $route_params = array_merge([ $name ], $params);
        return (!$absolute) ? Yii::$app->urlManager->createUrl($route_params) : Yii::$app->urlManager->createAbsoluteUrl($route_params);
    }

    public function dump($value)
    {
        echo PHP_EOL.PHP_EOL;
        var_dump($value);
        echo PHP_EOL.PHP_EOL;
    }

    public function print_r($value)
    {
        echo PHP_EOL.PHP_EOL;
        print_r($value);
        echo PHP_EOL.PHP_EOL;
    }

    public function disp($value, $color = false)
    {
        if ($color){
            $value = $this->ansiFormat($value, Console::FG_YELLOW, Console::NORMAL);
        }
        echo PHP_EOL . $value . PHP_EOL;

    }
}