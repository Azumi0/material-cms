<?php

namespace app\modules\photos\controllers;

use materialhelpers\GDSpecial;
use Yii;
use yii\web\HttpException;
use app\components\AdminController;
use app\models\GPhotosTable;
use materialhelpers\Stat;
use materialhelpers\Convert;


class BackendController extends AdminController
{
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionAdd()
    {
        $view = array();

        if ($this->isPost()) {
            $cropper = explode('|', $this->getPost('cropper'));

            $view['dimensions'] = $dim = json_decode($cropper[2], true);
            $view['encodedDim'] = $cropper[2];
            $view['fieldname'] = $cropper[1];
            $view['button'] = $cropper[0];
            $view['container'] = $cropper[3];
            $view['aspect'] = $this->getAspectRatio($dim['w'], $dim['h']);
        }

        return $this->render('add', $view);
    }

    public function actionEdit()
    {
        $view = array();

        if ($this->isPost()) {
            $pDB = new GPhotosTable();
            $cropper = explode('|', $this->getPost('cropper'));

            $view['filename'] = $cropper[0];
            $view['current'] = $current = $pDB->getByName($cropper[0]);
            $view['dimensions'] = $dim = json_decode($cropper[1], true);
            $view['container'] = $cropper[2];
            $view['aspect'] = $this->getAspectRatio($dim['w'], $dim['h']);
        }

        return $this->render('edit', $view);
    }

    public function actionSave()
    {
        if ($this->isPost()) {
            $pDB = new GPhotosTable();
            $post = $this->getPost('photo');
            if (!empty($post)){
                Convert::dropImageCache($post['name']);
                $pDB->delete(false, array('name' => $post['name']));
                $pDB->save($post);

                $image = Convert::imageCache2('/uploads/photos/', $post['name'], $post['code']);
                echo json_encode(array('success' => true, 'image' => $image));
            } else {
                echo json_encode(array('success' => false));
            }
            die();
        }
    }

    public function actionUpload()
    {
        if (!is_dir(MAIN_PATH . '/uploads/')) {
            mkdir(MAIN_PATH . '/uploads/');
        }
        $output_dir = MAIN_PATH . '/uploads/photos/';
        if (!is_dir($output_dir)) {
            mkdir($output_dir);
        }

        $custom_error = [];

        if (isset($_FILES["myfile"])) {
            $ret = array();

            if (!is_array($_FILES["myfile"]["name"])) //single file
            {
                $fileName = $_FILES["myfile"]["name"];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $key = Stat::keyGen(20);
                $newfilename = $key . '.' . $ext;
                move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $newfilename);
                if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                    $canvasname = $key . '_canvas.png';

                    $options = array('preserveAlpha' => true, 'preserveTransparency' => true);
                    $thumb = new GDSpecial($output_dir . $newfilename, $options);
                    $thumb->specialResize(1920, 1080, 'alpha');
                    $thumb->save($output_dir . $canvasname, 'png');

                    $ret[] = array('name' => $canvasname, 'urld' => urlencode($canvasname));
                } else {
                    $custom_error['jquery-upload-file-error'] = "Błąd wgrywania pliku";
                }
            } else  //Multiple files, file[]
            {
                $fileCount = count($_FILES["myfile"]["name"]);
                for ($i = 0; $i < $fileCount; $i++) {
                    if (empty($custom_error)) {
                        $fileName = $_FILES["myfile"]["name"][$i];
                        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $key = Stat::keyGen(20);
                        $newfilename = $key . '.' . $ext;
                        move_uploaded_file($_FILES["myfile"]["tmp_name"][$i], $output_dir . $newfilename);

                        if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                            $canvasname = $key . '_canvas.png';

                            $options = array('preserveAlpha' => true, 'preserveTransparency' => true);
                            $thumb = new GDSpecial($output_dir . $newfilename, $options);
                            $thumb->specialResize(1920, 1080, 'alpha');
                            $thumb->save($output_dir . $canvasname, 'png');

                            $ret[] = array('name' => $canvasname, 'urld' => urlencode($canvasname));
                        } else {
                            $custom_error['jquery-upload-file-error'] = "Błąd wgrywania pliku";
                        }
                    }
                }

            }

            if (!empty($custom_error)) {
                echo json_encode($custom_error);
            } else {
                echo json_encode($ret);
            }
        }
        die();
    }

    public function actionDisplay(){
        if (isset($_GET['file'])) {
            $finame = urldecode($_GET['file']);
            $file = MAIN_PATH . '/uploads/photos/' . $finame;
            $ext = strrchr($finame, '.');
            if (file_exists($file) && is_readable($file))  {
                Yii::$app->response->headers->set("Content-type", "application/\"$ext\"");
                Yii::$app->response->headers->set("Content-Disposition", "inline; filename=\"$finame\"");
                readfile($file);
            }
        } else {
            throw new HttpException(404, 'Plik nie został znaleziony');
        }
    }

    protected function gcd($a, $b)
    {
        return ($a % $b) ? $this->gcd($b, $a % $b) : $b;
    }

    protected function getAspectRatio($w, $h)
    {
        $div = $this->gcd($w, $h);

        $w = (int)round($w / $div, 0);
        $h = (int)round($h / $div, 0);

        return array('w' => $w, 'h' => $h);
    }
}