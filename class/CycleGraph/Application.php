<?php

namespace CycleGraph;

class Application {
	private $_config;
	private $_doctrine_connection;
	private $_doctrine_config;
	private $_entity_manager;
	
	public function __construct(Config $config) {
		$this->_config = $config;
	}
	
	public function getDoctrineConfig() {
		if(!$this->_doctrine_config) {
			$config = new \Doctrine\ORM\Configuration();
			//$config->setAutoGenerateProxyClasses(false);

			$cache = new \Doctrine\Common\Cache\ApcCache();

			$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(CLASS_PATH . 'CycleGraph/ORM'));

			$config->setProxyDir(CLASS_PATH . 'CycleGraph/ORM/Proxy');
			$config->setProxyNamespace("CycleGraph\ORM\\Proxy");
			
			$this->_doctrine_config = $config;
		}

		return $this->_doctrine_config;
	}
	
	public function getDoctrineConnection() {
		$db_options = $this->_config->getOption('database');

		$connect_options=array();
		$connect_options['driver'] = 'pdo_mysql';
		$connect_options['user'] = !empty($db_options['user']) ? $db_options['user'] : false;
		$connect_options['password'] = !empty($db_options['password']) ? $db_options['password'] : false;
		$connect_options['host'] = !empty($db_options['host']) ? $db_options['host'] : 'localhost';
		$connect_options['port'] = !empty($db_options['port']) ? $db_options['port'] : 3306;
		if(!empty($db_options['dbname']))
			$connect_options['dbname'] = $db_options['dbname'];
		else
			throw new \Exception('No database selected.');

		$connection = \Doctrine\DBAL\DriverManager::getConnection($connect_options, $this->getDoctrineConfig());
		$connection->setCharset('utf8');

		return $connection;
	}
	
	public function getEntityManager() {
		if(!$this->_entity_manager) {
			$connection = $this->getDoctrineConnection();
			$config = $this->getDoctrineConfig();
			
			$this->_entity_manager = \Doctrine\ORM\EntityManager::create($connection, $config);
		}
		
		return $this->_entity_manager;
	}
}