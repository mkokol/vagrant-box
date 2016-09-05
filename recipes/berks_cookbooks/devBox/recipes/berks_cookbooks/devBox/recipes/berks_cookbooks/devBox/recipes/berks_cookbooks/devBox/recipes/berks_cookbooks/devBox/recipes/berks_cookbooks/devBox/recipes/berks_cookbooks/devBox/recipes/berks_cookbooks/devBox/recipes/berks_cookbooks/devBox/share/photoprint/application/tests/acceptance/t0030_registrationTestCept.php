<?php

use AcceptanceHelpers\AbstractAcceptanceHelper;
use AcceptanceHelpers\CoreAcceptanceHelper;

$coreAcceptance = new CoreAcceptanceHelper($scenario);
$coreAcceptance->wantTo('Test user registration');

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->click(['link' => 'Регистрация']);
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->getRegistrationHelper()->registrationAsClient();
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);


$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_TESTS);


