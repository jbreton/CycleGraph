<?php

//Default time zone
date_default_timezone_set('America/New_York');

//Default encoding
mb_internal_encoding('UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');

define('BASE_PATH', realpath(__DIR__) . '/../');
define('CLASS_PATH', BASE_PATH.'class/');
define('LIB_PATH', BASE_PATH.'lib/');

//Class loaders
require CLASS_PATH . 'CycleGraph/ClassLoader.php';

$classLoader = new \CycleGraph\ClassLoader('Doctrine\\ORM');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib');
$classLoader->register();

$classLoader = new \CycleGraph\ClassLoader('Doctrine\\DBAL\\Migrations');
$classLoader->setIncludePath(LIB_PATH . 'doctrine-migrations/lib');
$classLoader->register();

$classLoader = new \CycleGraph\ClassLoader('Doctrine\\DBAL');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib/vendor/doctrine-dbal/lib');
$classLoader->register();

$classLoader = new \CycleGraph\ClassLoader('Doctrine\\Common');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib/vendor/doctrine-common/lib');
$classLoader->register();

$classLoader = new \CycleGraph\ClassLoader('Symfony');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib/vendor/');
$classLoader->register();

$classLoader = new \CycleGraph\ClassLoader("CycleGraph");
$classLoader->setIncludePath(CLASS_PATH);
$classLoader->register();

?>