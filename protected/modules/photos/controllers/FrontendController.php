<?php

namespace app\modules\photos\controllers;

use app\components\Controller;
use app\modules\photos\models\PhotoForm;
use materialhelpers\CorsCustom;
use materialhelpers\GDSpecial;
use materialhelpers\System;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\HttpException;
use app\models\GPhotosTable;
use materialhelpers\Convert;
use yii\web\Response;
use yii\web\UploadedFile;


class FrontendController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => CorsCustom::className(),
            ],
            [
                'class' => ContentNegotiator::className(),
                'only' => ['save', 'upload'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    public function actionOptions()
    {
        return [
            'success' => true,
        ];
    }

    public function actionSave()
    {
        $pDB = new GPhotosTable();
        $post = $this->getPost();

        if (!empty($post)){
            Convert::dropImageCache($post['name']);
            $pDB->delete(false, array('name' => $post['name']));
            $pDB->save($post);

            $image = Convert::imageCache2('/uploads/photos/', $post['name'], $post['code']);

            return ['success' => true, 'image' => $image];
        }

        return ['success' => false];
    }

    public function actionUpload()
    {
        $output_dir = System::createDirectoryStructure('uploads/photos');

        $form = new PhotoForm(['scenario' => 'add']);
        $form->photo = UploadedFile::getInstance($form, 'photo');

        if ($form->validate()) {
            $newfilename = $form->upload();
            $canvasname = $form->photoBaseName . '_canvas.png';

            $options = ['preserveAlpha' => true, 'preserveTransparency' => true];
            $thumb = new GDSpecial($output_dir . $newfilename, $options);
            $thumb->specialResize(1920, 1080, 'alpha');
            $thumb->save($output_dir . $canvasname, 'png');

            return [
                'success' => true,
                'name' => $canvasname,
                'urld' => urlencode($canvasname),
            ];
        }

        return [
            'success' => false,
            'errors' => $form->getErrors(),
        ];
    }

    public function actionDisplay(){
        if (isset($_GET['file'])) {
            $finame = urldecode($_GET['file']);
            $file = MAIN_PATH . '/uploads/photos/' . $finame;
            $ext = substr(strrchr($finame, '.'), 1);

            if (file_exists($file) && is_readable($file))  {
                Yii::$app->response->format = Response::FORMAT_RAW;
                header("Access-Control-Allow-Origin: *");
                header("Content-type: image/$ext");
                header("Content-Disposition: inline; filename=\"$finame\"");
                readfile($file);
                die();
            }
        }

        throw new HttpException(404, 'Plik nie został znaleziony');
    }
}