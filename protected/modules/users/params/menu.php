<?php

$items['USERS'][1][] = array(
    'label' => 'Użytkownicy',
    'url' => \Yii::$app->urlManager->createUrl('users/backend/index'),
    'linkOptions' => array('id' => 'susers'),
    'perms' => 'users|backend|index'
);

$menu[1]['USERS'] = array(
    'label' => 'Użytkownicy',
    'url' => \Yii::$app->urlManager->createUrl('users/backend/index'),
    'linkOptions' => array('id' => 'users'),
    'perms' => 'users|backend|index'
);
