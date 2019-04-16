<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('открываем home/join/login страницы');
$I->amOnPage('/');
$I->see('Welcome ёпта', 'h1');
$I->seeLink('Регистрация', '/user/join');
$I->seeLink('Вход', '/user/login');

$I->amOnPage('/user/join');
$I->see('Регистрация в системе:','h1');

$I->amOnPage('/user/login');
$I->see('Вход в систему:','h1');
