<?php

$routes[] = [
    'class' => 'yii\rest\UrlRule',
    'controller' => ['users' => 'users/frontend'],
    'patterns' => [
        'POST' => 'login',
        'DELETE' => 'logout',
        'PUT' => 'refresh',
        'GET basic' => 'getbasicuser',
        'POST register' => 'register',
        'PUT register' => 'edit',
        'PUT invoice' => 'invoice',
        'PUT extra' => 'extra',
        'OPTIONS' => 'options',
        'OPTIONS register' => 'options',
        'OPTIONS basic' => 'options',
        'OPTIONS invoice' => 'options',
        'OPTIONS extra' => 'options',
    ],
];
