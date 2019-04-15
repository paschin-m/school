<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('открываем home/join/login страницы');
$I->amOnPage('/');
$I->see('Welcome ёпта', 'h1');
$I->seeLink('Войти в систему:', '/user/join');
$I->seeLink('Вход в систему:', '/user/login');

$I->amOnPage('/user/join');
$I->see('Войти в систему:','h1');

$I->amOnPage('/user/login');
$I->see('Вход в систему:','h1');
