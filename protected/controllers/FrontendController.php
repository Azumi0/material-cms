<?php

namespace app\controllers;

use yii;
use app\components\Controller;
use app\modules\menu\models\Menu;
use yii\web\HttpException;

class FrontendController extends Controller
{
    public function actions()
    {
        return [
            'corsFilter' => [
                'class' => yii\filters\Cors::className(),
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    public function actionIndex()
    {
        throw new HttpException(404, 'Strona nie została znaleziona');
    }

    public function actionFile()
    {
        $req = Yii::$app->request;
        $domain_text = $req->getPathInfo();
        $dte = explode('/', $domain_text);
        unset($dte[0]);

        $size = sizeof($dte) - 1;

        $mDB = new Menu();

        $x = 0;
        $y = $size;
        $id = null;
        for (; $y >= 0; $y--) {
            $id = (int)$mDB->check(implode('/', $dte));
            if ($id) {
                break;
            }
            unset($dte[$y]);
            $x++;
        }

        if ($id === null) {
            throw new HttpException(404, 'Strona nie została znaleziona');
        }
        
        $data = $mDB->fetchRowByPrimary($id);

        if (empty($data) || $data['type'] != 4) {
            throw new HttpException(404, 'Strona nie została znaleziona');
        }

        if (isset($data['file']) && !empty($data['file'])) {
            $file = MAIN_PATH . '/uploads/menu/' . $data['file'];
            $finame = $data['file'];
            $ext = strrchr($finame, '.');
            if (file_exists($file) && is_readable($file))  {
                header("Content-type: application/\"$ext\"");
                header("Content-Disposition: attachment; filename=\"$finame\"");
                readfile($file);
                die();
            } else {
                throw new HttpException(404, 'Strona nie została znaleziona');
            }
        } else {
            throw new HttpException(404, 'Strona nie została znaleziona');
        }
    }
}