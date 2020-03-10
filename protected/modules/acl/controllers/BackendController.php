<?php

namespace app\modules\acl\controllers;

use Yii;
use app\components\AdminController;
use app\modules\acl\models\AclForm;
use materialhelpers\Convert;


class BackendController extends AdminController
{
    public function actionIndex()
    {
        $view = array();
        $view['currbyperm'] = 'acl|backend|index';

        $authMan = Yii::$app->authManager;
        $view['roles'] = $authMan->getRoles();

        return $this->render('index', $view);
    }

    public function actionAdd()
    {
        $view = array();
        $view['currbyperm'] = 'acl|backend|index';

        $form = new AclForm();

        if ($this->isPost()){
            $data = $this->getPost('AclForm');
            $perms = $this->getPost('perms', array());

            $form->attributes = $data;
            $data['name'] = Convert::removePL($data['description']);

            if ($form->validate()){
                $authMan = Yii::$app->authManager;
                $perms = $this->setSystemPermissions($perms);
                foreach ($perms as $perm) {
                    if ($authMan->getPermission($perm) === null) {
                        $cp = $authMan->createPermission($perm);
                        $authMan->add($cp);
                    }
                }

                $role = $authMan->createRole($data['name']);
                $role->description = $data['description'];
                $authMan->add($role);

                foreach ($perms as $perm) {
                    $tmp = $authMan->getPermission($perm);
                    $authMan->addChild($role, $tmp);
                }

                $this->setFlash('success', 'Nowa rola została dodana prawidłowo');
                return $this->redirect($this->url('acl/backend/index'));
            } else{
                $this->setFlash('error', 'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.');
                $view['errors'] = $form->getErrors();
            }
        }
        $view['perms'] = $this->getPermissions();

        $view['model'] = $form;

        return $this->render('add', $view);
    }

    public function actionEdit()
    {
        $view = array();
        $view['currbyperm'] = 'acl|backend|index';

        $name = $this->getParam('name');
        if (!$name) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('acl/backend/index'));
        }

        $authMan = Yii::$app->authManager;
        $crole = $authMan->getRole($name);

        $form = new AclForm();
        $form->attributes = array('description' => $crole->description);

        if ($this->isPost()){
            $data = $this->getPost('AclForm');
            $perms = $this->getPost('perms', array());

            $form->attributes = $data;
            $data['name'] = Convert::removePL($data['description']);

            if ($form->validate()){
                $authMan->removeChildren($crole);
                $perms = $this->setSystemPermissions($perms);
                foreach ($perms as $perm) {
                    if ($authMan->getPermission($perm) === null) {
                        $cp = $authMan->createPermission($perm);
                        $authMan->add($cp);
                    }
                }

                if ($crole->description != $data['description']){
                    $crole->name = $data['name'];
                    $crole->description = $data['description'];
                    $authMan->update($name, $crole);

                    $role = $authMan->getRole($data['name']);
                } else {
                    $role = $authMan->getRole($name);
                }

                foreach ($perms as $perm) {
                    $tmp = $authMan->getPermission($perm);
                    $authMan->addChild($role, $tmp);
                }

                $this->setFlash('success', 'Modyfikacja wybranej roli powiodła się');
                return $this->redirect($this->url('acl/backend/index'));
            } else{
                $this->setFlash('error', 'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.');
                $view['errors'] = $form->getErrors();
            }
        }
        $view['perms'] = $this->getPermissions();
        $aperms = $authMan->getPermissionsByRole($name);
        $view['aperms'] = array_keys($aperms);

        $view['model'] = $form;

        return $this->render('edit', $view);
    }

    public function actionDelete() {
        $name = $this->getParam('name');
        if (!$name) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('acl/backend/index'));
        }

        $authMan = Yii::$app->authManager;
        $crole = $authMan->getRole($name);
        $authMan->remove($crole);

        $this->setFlash('success', 'Rola została usunięta');

        return $this->redirect($this->url('acl/backend/index'));
    }

    protected function setSystemPermissions($perms) {
        $perms[] = 'materialcms|backend|403';
        $perms[] = 'materialcms|backend|index';
        $perms[] = 'materialcms|backend|sidebar';

        return $perms;
    }

    protected function getPermissions() {
        $permissions = array();

        if (YII_DEBUG || !is_file(PROTECTED_PATH . '/runtime/materialhelpers/permissions.php')) {
            $fh = fopen(PROTECTED_PATH . '/runtime/materialhelpers/permissions.php', 'w');
            fwrite($fh, "<?php\n\n");

            $dh = opendir(PROTECTED_PATH . '/modules/');
            while ($dir = readdir($dh)) {
                if ($dir != '.' && $dir != '..') {
                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/permissions.php')) {
                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/permissions.php')) . "\n");
                        if (is_dir(PROTECTED_PATH . '/modules/' . $dir . '/modules/')) {
                            $dhs = opendir(PROTECTED_PATH . '/modules/' . $dir . '/modules/');
                            while ($dirs = readdir($dhs)) {
                                if ($dirs != '.' && $dirs != '..') {
                                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/permissions.php')) {
                                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/permissions.php')) . "\n");
                                    }
                                }
                            }
                            closedir($dhs);
                        }
                    }
                }
            }

            fwrite($fh, "\n?>");
            fclose($fh);

            include PROTECTED_PATH . '/runtime/materialhelpers/permissions.php';
        } else {
            include PROTECTED_PATH . '/runtime/materialhelpers/permissions.php';
        }

        return $permissions;
    }
}