<?php
use cafetapi\io\UserManager;
use cafetapi\user\Perm;
use cafetapi\io\DatabaseConnection;
use cafetapi\io\ClientManager;

require 'cafetapi_load.php';

$m = UserManager::getInstance();

var_dump($m);

$u = $m->getUserById(42);

var_dump($u);

var_dump($m->getUser($u->getPseudo()));