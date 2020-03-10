<?php

namespace app\modules\admins\controllers;

use Yii;
use app\components\AdminController;
use app\modules\admins\models\AdminForm;
use app\modules\admins\models\GAdmin;
use materialhelpers\Stat;


class BackendController extends AdminController
{
    public function actionIndex()
    {
        $view = array();
        $view['currbyperm'] = 'admins|backend|index';

        $view['limit'] = $limit = 10;

        $aDB = new GAdmin();

        $row = $aDB->getListingAjaxCount();
        $view['count'] = $count = $row['count'];

        if ($this->isAjax()) {
            $post = $this->getPost();
            $limit = $post['limit'];

            if ($post['search']) {
                $row = $aDB->getListingAjaxCount($post['search']);
                $count = $row['count'];

                if ($count < $post['offset']) {
                    $post['offset'] = 0;
                }
            }
            $view['offset'] = $post['offset'];

            $view['listing'] = $aDB->getListingAjax($post['search'], $limit, $post['offset'], $post['order'] . ' ' . $post['direction']);

            $html = $this->renderPartial('_listing', $view);
            echo json_encode(array('html' => $html, 'count' => $count, 'page' => ($post['offset'] / $limit) + 1)); die;
        } else {
            return $this->render('index', $view);
        }
    }

    public function actionAdd()
    {
        $view = array();
        $view['currbyperm'] = 'admins|backend|index';

        $form = new AdminForm(['scenario' => 'add']);
        $authMan = Yii::$app->authManager;

        if ($this->isPost()){
            $data = $this->getPost('AdminForm');

            $form->attributes = $data;
            if ($form->validate()){
                unset($data['rpassword']);
                if (empty($data['avatar'])){
                    unset($data['avatar']);
                }

                $data['active'] = 1;
                $data['salt'] = $salt = Stat::keyGen(5);

                $data['password'] = Yii::$app->getSecurity()->generatePasswordHash($salt . $data['password']);

                $aDB = new GAdmin();
                $id = $aDB->save($data);

                $crole = $authMan->getRole($data['role']);
                $authMan->assign($crole, $id);

                $this->setFlash('success', 'Nowe konto administratora zostało dodane prawidłowo');
                return $this->redirect($this->url('admins/backend/index'));
            } else {
                $form->password = null;
                $form->rpassword = null;

                $this->setFlash('error', 'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.');
                $view['errors'] = $form->getErrors();
                $view['avatar'] = $data['avatar'];
            }
        }

        $sRoles = $authMan->getRoles();;

        $roles = [];

        foreach ($sRoles as $sRole) {
            $roles[$sRole->name] = $sRole->description;
        }

        $view['roles'] = $roles;

        $view['model'] = $form;

        return $this->render('add', $view);
    }

    public function actionEdit()
    {
        $view = array();
        $view['currbyperm'] = 'admins|backend|index';

        $id = $this->getParam('id');
        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('admins/backend/index'));
        }

        $aDB = new GAdmin();
        $cdata = $aDB->fetchRowByPrimary($id, 'mail, realname, avatar, role, banner');
        $view['avatar'] = $cdata['avatar'];

        $form = new AdminForm(['scenario' => 'edit']);
        $form->attributes = $cdata;

        $authMan = Yii::$app->authManager;

        if ($this->isPost()){
            $data = $this->getPost('AdminForm');

            $form->attributes = $data;
            $form->rpassword = $data['rpassword'];
            if ($form->validate()){
                unset($data['rpassword']);
                if (empty($data['avatar'])){
                    $data['avatar'] = null;
                }

                if ($data['password']) {
                    $data['salt'] = $salt = Stat::keyGen(5);

                    $data['password'] = Yii::$app->getSecurity()->generatePasswordHash($salt . $data['password']);
                } else {
                    unset($data['password']);
                }

                $aDB->save($data, $id);

                $authMan->revokeAll($id);

                $crole = $authMan->getRole($data['role']);
                $authMan->assign($crole, $id);

                $this->setFlash('success', 'Modyfikacja wybranego konta administratora powiodła się');
                return $this->redirect($this->url('admins/backend/index'));
            } else {
                $form->password = null;
                $form->rpassword = null;

                $this->setFlash('error', 'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.');
                $view['errors'] = $form->getErrors();
                $view['avatar'] = $data['avatar'];
            }
        }

        $sRoles = $authMan->getRoles();;

        $roles = [];

        foreach ($sRoles as $sRole) {
            $roles[$sRole->name] = $sRole->description;
        }

        $view['roles'] = $roles;

        $view['model'] = $form;

        return $this->render('edit', $view);
    }

    public function actionDelete() {
        $id = $this->getParam('id');
        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('admins/backend/index'));
        }

        $authMan = Yii::$app->authManager;
        $authMan->revokeAll($id);

        $aDB = new GAdmin();
        $aDB->delete($id);

        $this->setFlash('success', 'Konto administratora zostało usunięte');

        return $this->redirect($this->url('admins/backend/index'));
    }

    public function actionBlock() {
        $id = $this->getParam('id');
        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('admins/backend/index'));
        }

        if ($id > 1) {
            $aDB = new GAdmin();
            $flag = $aDB->flag($id, 'active');

            if ($flag) {
                $this->setFlash('success', 'Konto administratora zostało odblokowane');
            } else {
                $this->setFlash('success', 'Konto administratora zostało zablokowane');
            }
        } else {
            $this->setFlash('error', 'Przykro nam nie możesz zablokować konta tego administratora');
        }

        return $this->redirect($this->url('admins/backend/index'));
    }
}