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

$classLoader = new \Kronos\Common\ClassLoader('Doctrine\\ORM');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib');
$classLoader->register();

$classLoader = new \Kronos\Common\ClassLoader('Doctrine\\DBAL\\Migrations');
$classLoader->setIncludePath(LIB_PATH . 'doctrine-migrations/lib');
$classLoader->register();

$classLoader = new \Kronos\Common\ClassLoader('Doctrine\\DBAL');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib/vendor/doctrine-dbal/lib');
$classLoader->register();

$classLoader = new \Kronos\Common\ClassLoader('Doctrine\\Common');
$classLoader->setIncludePath(LIB_PATH . 'doctrine2/lib/vendor/doctrine-common/lib');
$classLoader->register();

$classLoader = new \Kronos\Common\ClassLoader("CycleGraph");
$classLoader->setIncludePath(CLASS_PATH);
$classLoader->register();

?>