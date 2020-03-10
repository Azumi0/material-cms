<?php

namespace app\components;

use app\models\GAccessTokensTable;
use Yii;
use yii\web\Controller AS YiiController;
use yii\web\UnauthorizedHttpException;

class Controller extends YiiController
{
    public $frontendGuestAccess = array();
    public $uid = 0;
    public $userUsedToken = '';

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws UnauthorizedHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action) {
        parent::beforeAction($action);

        defined('default_lang') or define('default_lang', 'pl');

        \Yii::$app->language = (Yii::$app->session['lang']) ? Yii::$app->session['lang'] : 'pl';
        $this->frontendGuestAccess = Yii::$app->params['frontendGuestAccess'];
        $auth = Yii::$app->user;
        $module = \Yii::$app->controller->module;
        if ($module) {
            $parent = $module->module;
            if (!empty($parent) && $parent->id != 'materialcms'){
                $module = $parent->id . '/' . $module->id;
            } else {
                $module = $module->id;
            }
        } else {
            $module = 'default';
        }
        $controller = \Yii::$app->controller->id;
        $action = \Yii::$app->controller->action->id;


        if (!$auth->isGuest && $auth->identity->type == 'frontend') {
            $this->uid = $auth->identity->id;
        } else {
            $token = $this->getBearerToken();

            if ($token !== null) {
                $gAcTkDB = new GAccessTokensTable();
                $tokenExists = $gAcTkDB->getValidByToken($token);
                if (!empty($tokenExists)) {
                    $identity = FrontendIdentity::findIdentity($tokenExists['user_id']);
                    if (!empty($identity)) {
                        Yii::$app->user->login($identity);
                        $this->uid = $identity->id;
                        $this->userUsedToken = $token;
                    }
                }
            }
        }


        if ($auth->isGuest && !in_array($module . '|' . $controller . '|' . $action, $this->frontendGuestAccess)) {
            throw new UnauthorizedHttpException();
        }

        return true;
    }

    public function getParam($name, $default = false)
    {
        return Yii::$app->request->getQueryParam($name, $default);
    }

    public function getGet($name, $default = false)
    {
        return Yii::$app->request->get($name, $default);
    }

    public function getPost($name = null, $default = false)
    {
        return Yii::$app->request->post($name, $default);
    }

    public function getReferrer()
    {
        return Yii::$app->request->referrer;
    }

    public function getHeaders()
    {
        return Yii::$app->request->getHeaders();
    }

    /**
     * get access token from header
     * */
    function getBearerToken() {
        $headers = $this->getHeaders();
        $auth = $headers->get('Authorization', null);

        if ($auth !== null) {
            if (preg_match('/Bearer\s(\S+)/', $auth, $matches)) {
                return $matches[1];
            }
        }

        return null;
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
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }

    public function setCookie($name, $value, $time)
    {
        Yii::$app->response->cookies->add(new \yii\web\Cookie([ 'name' => $name, 'value' => $value, 'expire' => $time ]));
    }

    public function getCookie($name)
    {
        $cookies = Yii::$app->response->cookies;
        return $cookies->getValue($name, false);
    }

    public function removeCookie($name)
    {
        $cookies = Yii::$app->response->cookies;
        $cookies->remove($name);
    }

    public function setFlash($type, $message)
    {
        $msg = array($type, $message);
        Yii::$app->session->addFlash('shmessages', $msg);
    }

    public function getFlash()
    {
        return Yii::$app->session->getFlash('shmessages', false);
    }

    public function setSession($key, $value)
    {
        Yii::$app->session->set($key, $value);
        return true;
    }

    public function getSession($key, $defaultValue = null)
    {
        return Yii::$app->session->get($key, $defaultValue);
    }

    public function unsetSession($key)
    {
        return Yii::$app->session->remove($key);
    }
}