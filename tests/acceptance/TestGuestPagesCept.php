<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('открываем home/join/login страницы');
$I->amOnPage('/');
$I->see('Welcome ёпта', 'h1');
$I->seeLink('Join', '/site/join');
$I->seeLink('Join', '/site/login');

$I->amOnPage('/site/join');
$I->see('Join us','h1');

$I->amOnPage('/site/login');
$I->see('Login','h1');

