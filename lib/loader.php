<?php
require_once 'config.php';
require_once 'db.php';
require_once 'app.php';
require_once 'site.php';

$config = new Config(__DIR__.'/../config.json');
$db = new Db($config);
$app = new App($config, $db);
$rootUrl = preg_replace('/index.php$/', '', $_SERVER['REQUEST_URI']);
