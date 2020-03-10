<?php

namespace app\components;

use yii\base\Widget;
use app\modules\menu\models\Menu;

class MenuWidget extends Widget
{
    public $type = 1;
    public $modpath = '';
    public $orgpage = '';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $view = array();

        $view['type'] = $this->type;
        $view['modpath'] = $this->modpath;
        $view['orgpage'] = $this->orgpage;

        $mDB = new Menu();
        $view['menu'] = $mDB->getFrontendListing($this->type);


        return $this->render('//MenuWidget.twig', $view);
    }
}