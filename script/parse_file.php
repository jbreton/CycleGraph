<?php

require_once(__DIR__.'/../config/bootstrap.php');

$config = new \CycleGraph\Config(BASE_PATH . '/config/config.ini');
$app = new \CycleGraph\Application($config);

$parser = new \CycleGraph\RideLog\Parser($app->getEntityManager());
$parser->ParseFile($_SERVER['argv'][1]);