<?php

require_once(__DIR__.'/../config/bootstrap.php');

$config = new \CycleGraph\Config(BASE_PATH . '/config/config.ini');
$app = new \CycleGraph\Application($config);

$em = $app->getEntityManager();

$route = $em->find('\CycleGraph\ORM\Entity\Route', 1);
if(!$route) {
	$route = new \CycleGraph\ORM\Entity\Route();
	echo "New route\n";
}
else {
	echo $route->id."\n";
	foreach($route->rides as $ride) {
		echo "\t$ride->id\n";
	}
	
}
$pathinfo = pathinfo($_SERVER['argv'][1]);
$route->name = $pathinfo['filename'];
$route->description = '';

$parser = new \CycleGraph\RideLog\Parser($em);
$parser->ParseFile($route, $_SERVER['argv'][1]);