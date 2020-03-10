<?php

namespace app\modules\auth\controllers;

use Yii;
use app\components\AdminController;
use app\components\BackendIdentity;
use app\modules\auth\models\LoginForm;


class BackendController extends AdminController
{
    public function actionLogin()
    {
        $view = array();

        $view['rawPage'] = true;
        $view['loginPage'] = true;
        $view['bodyclass'] = 'login-page';

        $form = new LoginForm();

        if ($this->isPost()){
            $data = $this->getPost('LoginForm');

            $form->attributes = $data;

            if ($form->validate()){
                $identity = BackendIdentity::authenticate($data['mail'], $data['password']);
                if (!empty($identity)){
                    Yii::$app->user->login($identity);

                    return $this->redirect($this->url('backend/index'));
                } else {
                    unset($form);
                    $form = new LoginForm();
                    $this->setFlash('error', 'Nieprawidłowy adres e-mail lub hasło.');
                }
            } else{
                $this->setFlash('error', 'Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.');
                $view['errors'] = $form->getErrors();
            }
        }

        $view['model'] = $form;

        return $this->render('login', $view);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        $this->setFlash('success', 'Dziękujemy za używanie naszego systemu CMS. Do zobaczenia!');

        return $this->redirect($this->url('auth/backend/login'));
    }
}