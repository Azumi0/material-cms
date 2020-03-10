<?php

namespace app\components;

use yii\base\Widget;

class BreadcrumbsWidget extends Widget
{
    public $data;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (empty($this->data) || !isset($this->data)){
            return '';
        }

        $view = array();

        $view['data'] = $this->data;

        return $this->render('//frontend/BreadcrumbsWidget.twig', $view);
    }
}