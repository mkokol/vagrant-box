<?php

use AcceptanceHelpers\AbstractAcceptanceHelper;
use AcceptanceHelpers\CoreAcceptanceHelper;

$coreAcceptance = new CoreAcceptanceHelper($scenario);

$coreAcceptance->wantTo('Log in as user');
$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->getUserHelper()->loginAsClient();
$coreAcceptance->seeInCurrentUrl('/ru/');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/produces/create/item/a5');
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->click(['link' => 'Загрузить фото']);
$coreAcceptance->waitForElement('#mbox-wnd', 30);
$coreAcceptance->waitForJS('return mboxInProgres == false;', 30);
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->fillField('title', 'Test Img Title');
$coreAcceptance->attachFile('img', 'test-upload.jpg');
$coreAcceptance->click('Загрузить фотографию');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->dragAndDrop('#0', '#insert-img1');
//$coreAcceptance->click('.basketbtn');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_TESTS);
