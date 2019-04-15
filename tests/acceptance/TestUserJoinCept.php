<?php
use Step\Acceptance\TestUserJoin;

$I = new TestUserJoin($scenario);
$I->wantTo('New users join and logn');

$user1=$I->imagineUser();
$user2=$I->imagineUser();

$I->loginUser($user1);
$I->see("Такого пользователя нет");

$I->joinUser($user1);
$I->joinUser($user2);

$I->joinUser($user1);
$I->see("пользователь уже существует");

$I->loginUser($user1);
$I->isUserLOgged($user1);
$I->noUserLogged($user2);
$I->logoutUser();

$I->loginUser($user2);
$I->isUserLOgged($user2);
$I->noUserLogged($user1);
$I->logoutUser();


$user1["password"]="errorpass";

$I->loginUser($user1);
$I->see('Неверный пароль пользователя');
