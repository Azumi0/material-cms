<?php

namespace app\controllers;

use Yii;
use app\components\AdminController;
use app\models\GSidebarsTable;

class BackendController extends AdminController
{
    public function beforeAction($action) {
        $acId = \Yii::$app->controller->action->id;
        if ($acId == 'sidebar') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->redirect($this->url('users/backend/index'));
    }

    public function action403()
    {
        return $this->render('403');
    }

    public function actionSidebar()
    {
        if ($this->isAjax() && $this->isPost()) {
            $gsbDB = new GSidebarsTable();
            $data = $this->getPost();

            if (isset($data['sbLeft'])){
                $gsbDB->save(array('sbLeft' => $data['sbLeft']), $this->aid);

                echo json_encode(array('success' => true));
            } elseif (isset($data['sbRight'])){
                $gsbDB->save(array('sbRight' => $data['sbRight']), $this->aid);

                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('success' => false));
            }
            die();
        } else {
            echo json_encode(array('success' => false));
            die();
        }
    }
}