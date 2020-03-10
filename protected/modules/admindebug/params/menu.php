<?php

$items['Debug'][1][] = array(
    'label' => 'Debug',
    'url' => \Yii::$app->urlManager->createUrl('admindebug/backend/index'),
    'linkOptions' => array('id' => 'sadmindebug'),
    'perms' => 'admindebug|backend|index'
);

$menu[99]['Debug'] = array(
    'label' => 'Debug',
    'url' => \Yii::$app->urlManager->createUrl('admindebug/backend/index'),
    'linkOptions' => array('id' => 'admindebug'),
    'perms' => 'admindebug|backend|index'
);

