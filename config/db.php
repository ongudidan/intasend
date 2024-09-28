<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=intasend',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

$host = $_SERVER['HTTP_HOST']; // Get the current host

if ($host === 'localhost') {
    // Localhost environment
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=intasend',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ];
} elseif ($host === 'intasend.doubledeals.co.ke') {
    // Production environment for doubledeals.co.ke
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=intasend',
        'username' => 'dandeal_danny',
        'password' => 'I4QG&5C02mj[',
        'charset' => 'utf8',
    ];
} elseif ($host === 'delta.wuaze.com') {
    // Production environment for wuaze.com
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=sql110.infinityfree.com;dbname=if0_37114096_delta',
        'username' => 'if0_37114096',
        'password' => 'QcIDYuIrKJ',
        'charset' => 'utf8',
    ];
} else {
    // Default fallback or other environments
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=mariadb;dbname=basic',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ];
}

