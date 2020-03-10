<?php

namespace app\components;

use Yii;
use yii\web\Controller AS YiiController;
use materialhelpers\MenuGenerator;
use materialhelpers\Stat;

class AdminController extends YiiController
{
    public $breadcrumbs = array();
    public $urole;
    public $aid;
    public $currmodule;

    public function beforeAction($action) {
        parent::beforeAction($action);

        defined('default_lang') or define('default_lang', 'pl');

        \Yii::$app->language = (Yii::$app->session['admin_lang']) ? Yii::$app->session['admin_lang'] : 'pl';

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

        $guest = array(
            'auth|backend|login',
            'auth|backend|logout',
            'materialcms|backend|error',
            'materialcms|backend|403',
        );

        MenuGenerator::$module = $module;
        MenuGenerator::$controller = $controller;
        MenuGenerator::$action = $action;
        MenuGenerator::$params = Yii::$app->request->getQueryParams();

        $auth = Yii::$app->user;
        $identity = Yii::$app->user->identity;
        if (!$auth->isGuest && $identity->type == 'frontend') {
            $this->redirect($this->url('auth/backend/logout'));
            return false;
        } else {
            if (!$auth->isGuest) {
                $this->urole = $identity->role;
                $this->aid = $identity->id;
            }
            MenuGenerator::$role = $this->urole;
            Stat::$role = $this->urole;
            /*$this->dump(($module . '|' . $controller . '|' . $action == 'materialcms|backend|index' && $auth->isGuest));
            $this->dump($module . '|' . $controller . '|' . $action); die();*/

            $this->currmodule = $module;
            if ($module . '|' . $controller . '|' . $action == 'materialcms|backend|index' && $auth->isGuest) {
                $this->redirect($this->url('auth/backend/login'));
                return false;
            } elseif (in_array($module . '|' . $controller . '|' . $action, $guest)) {
                return true;
            } else {
                if ($auth->isGuest) {
                    $this->redirect($this->url('auth/backend/login'));
                    return false;
                } elseif ($this->urole == 'su') {
//                    self::$submodules = ModulesAutoloader::getSubmodules($module);
                    return true;
                } elseif ($auth->can($module . '|' . $controller . '|' . $action)) {
//                    self::$submodules = SHModulesAutoloader::getSubmodules($module);
                    return true;
                } else {
                    $this->redirect($this->url('backend/403'));
                    return false;
                }
            }
        }
//        return true;
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