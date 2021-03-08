<?php
require '../vendor/autoload.php';

$config = [
    'username' => 'hdb66hy',
    'password_key' => 'CuksgBV1SyHYu0eg',
    'tpId' => '30960',
    //valid_code
    'signature' => '【BangTech】',
];

$client = new \Bangtch\ztdxsms\lib\Client([
    'password_key' => $config['password_key'],
    'username' => $config['username'],
]);
try {
    $res = $client->setSignature($config['signature'])
        ->setTpId($config['tpId'])
        ->setParam(['17719496221'], [['valid_code' => '5647']])
        ->sendSms();
    echo $res;
} catch (\Bangtch\ztdxsms\exception\DataIllegalException $exception) {
    echo $exception->getMessage();
}

