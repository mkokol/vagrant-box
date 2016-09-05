<?php

use AcceptanceHelpers\AbstractAcceptanceHelper;
use AcceptanceHelpers\CoreAcceptanceHelper;

$coreAcceptance = new CoreAcceptanceHelper($scenario);
$coreAcceptance->wantTo('Test user registration');

$coreAcceptance->amOnPage('/ru/');

$coreAcceptance->click(['link' => 'Оплата']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
$coreAcceptance->see('Способы оплаты', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->click(['link' => 'Доставка']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->see('Форма доставки', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->click(['link' => 'Справка']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->see('Своим клиентам мы не просто продаем продукт, мы гарантируем высокое качество в кратчайшие сроки.', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->click(['link' => 'Дизайнерам']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->see('Предложение для дизайнеров.', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->click(['link' => 'Вебмастерам']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->see('Предложение для продавцов.', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->click(['link' => 'Бизнесу']);
$coreAcceptance->waitForJS('return $.active == 0;', 30);
$coreAcceptance->see('Предложение для компаний, интернет-проектов, сайтов и организаций.', 'h1');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

// Test home page link

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-tshirt');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-puzzle');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-cup');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-trivet');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-mousepad');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

$coreAcceptance->amOnPage('/ru/');
$coreAcceptance->click('.t-link-products-postcard');
$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

// Test home page link from slider

// TODO MOUSE HOVER FOR SLIDER
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-tshirt');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
//
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-puzzle');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
//
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-cup');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
//
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-trivet');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
//
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-mousepad');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
//
//$coreAcceptance->amOnPage('/ru/');
//$coreAcceptance->click('.t-slider-products-postcard');
//$coreAcceptance->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);
