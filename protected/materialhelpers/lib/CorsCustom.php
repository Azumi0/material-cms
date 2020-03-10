<?php
namespace materialhelpers;

use Yii;
use yii\filters\Cors;

class CorsCustom extends Cors

{
    /**
     * @param $action
     * @return bool
     * @throws \yii\base\ExitException
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()->getHeaders()->set('Allow', 'POST GET PUT DELETE OPTIONS');
            Yii::$app->end();
        }

        return true;
    }
}

