<?php

$routes[] = [
    'class' => 'yii\rest\UrlRule',
    'controller' => ['photos' => 'photos/frontend'],
    'patterns' => [
        'POST' => 'save',
        'POST upload' => 'upload',
        'GET' => 'display',
        'OPTIONS' => 'options',
        'OPTIONS upload' => 'options',
    ],
    'tokens' => [
        '{file}' => '<file:(.*?)>',
    ]
];
