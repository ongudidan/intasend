<?php

use IntaSend\IntaSendPHP\Checkout;

$credentials = [
    'token' => 'ISSecretKey_test_691abd3d-84d5-4c9b-a4e1-801a4aa7e404',
    'publishable_key' => 'ISPubKey_test_c1825e70-974c-4fdb-861f-cec6ae1d1d2d',
    'test' => true,
];
$checkout = new Checkout();
$checkout->init($credentials);