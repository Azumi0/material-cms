<?php

namespace app\modules\admindebug\controllers;

use Yii;
use app\components\AdminController;


class BackendController extends AdminController
{
    public function actionIndex()
    {
        $view = array();

        $appLogPath = PROTECTED_PATH . "/runtime/logs/app.log";
        $content = '';

        if (is_readable($appLogPath)) {
            $file = file($appLogPath);

            $count = count($file);

            if ($count > 200) {
                $start = $count - 200;
            } else {
                $start = 0;
            }

            for ($i = $start; $i < $count; $i++) {
                $content .= $file[$i] . "<br />";
            }
        }

        $view['content'] = $content;

        $menuFilePath = PROTECTED_PATH . "/runtime/materialhelpers/menu.php";
        $scontent = '';

        if (is_readable($menuFilePath)) {
            $sfile = file($menuFilePath);

            $scount = count($sfile);

            for ($x = 0; $x < $scount; $x++) {
                $scontent .= $sfile[$x] . "<br />";
            }
        }

        $view['scontent'] = $scontent;

        return $this->render('index.twig', $view);
    }

    public function actionClearlogs()
    {
        $cache_path = PROTECTED_PATH . "/runtime/logs/";

        $this->clear($cache_path);
        $this->setFlash('success', 'Logi aplikacji zostały wyczyszczone');

        return $this->redirect($this->url('admindebug/backend/index'));
    }

    public function actionCleartwig()
    {
        $cache_path = PROTECTED_PATH . "/runtime/Twig/cache/";

        $this->clear($cache_path);
        $this->setFlash('success', 'Cache twig zostało wyczyszczone');

        return $this->redirect($this->url('admindebug/backend/index'));
    }

    public function actionClearsystem()
    {
        $cache_path = PROTECTED_PATH . "/runtime/materialhelpers/";

        $this->clear($cache_path);
        $this->setFlash('success', 'Cache systemu zostało wyczyszczone');

        return $this->redirect($this->url('admindebug/backend/index'));
    }

    public function actionClearimages()
    {
        $cache_path = MAIN_PATH . "/public/i/";

        $this->clear($cache_path);
        $this->setFlash('success', 'Wygenerowane obrazki zostały wyczyszczone');

        return $this->redirect($this->url('admindebug/backend/index'));
    }

    protected function clear($path) {
        $dh = opendir($path);

        while ($f = readdir($dh)) {
            if ($f != '..' && $f != '.') {
                $p = $path . $f;
                if (is_dir($p)) { $this->clear($p . '/'); rmdir($p); } else { @unlink($p); }
            }
        }

        closedir($dh);
    }
}