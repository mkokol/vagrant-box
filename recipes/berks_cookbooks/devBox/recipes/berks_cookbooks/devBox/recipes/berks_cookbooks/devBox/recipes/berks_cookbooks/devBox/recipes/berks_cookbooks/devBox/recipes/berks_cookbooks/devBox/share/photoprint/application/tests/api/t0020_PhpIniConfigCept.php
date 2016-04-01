<?php 

$I = new ApiTester($scenario);
$I->wantTo('Test php.ini settings');

$I->sendGet('/ru/php-test/upload-params');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains('{"status":"success","upload_max_filesize":"24M","post_max_size":"24M"}');
