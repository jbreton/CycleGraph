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
	
	public function run() {
		if($_GET['action']) {
			// TODO
		}
		else {
			$this->showHeader();
			
			$em = $this->getEntityManager();
			
			$ride = $em->find('\CycleGraph\ORM\Entity\Ride', 7);
			
			$series = array(
				'speed' => array('name' => 'Vitesse', 'data' => array()),
				'max_speed' => array('name' => 'Vitesse max', 'data' => array()),
				'avg_speed' => array('name' => 'Vitesse moy', 'data' => array()),
				'relative_avg_speed' => array('name' => 'Vitesse avg relative', 'data' => array())
			);
			
			$i = 0;
			foreach($ride->points as $point) {
				$time = new \DateTime($point->real_time);
				
				$series['speed']['data'][] = array((int)($time->format('U').'000')+2577600000, (float)$point->speed);
				$series['max_speed']['data'][] = array((int)($time->format('U').'000')+2577600000, (float)$ride->max_speed);
				$series['avg_speed']['data'][] = array((int)($time->format('U').'000')+2577600000, (float)$ride->avg_speed);
				$series['relative_avg_speed']['data'][] = array((int)($time->format('U').'000')+2577600000, (float)$point->avg_speed);
			}
			
			echo '<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
		console.debug($(\'.paste_gpx\'));
		$(\'#paste_gpx\').button().click(function() {
			console.debug(\'click\');
		});
		
		
        chart = new Highcharts.Chart({
            chart: {
                renderTo: \'container\',
                type: \'spline\',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: \''.$ride->name.'\',
                x: -20 //center
            },
            xAxis: {
				type:\'datetime\',
            },
            yAxis: {
                title: {
                    text: \'Speed\'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: \'#808080\'
                }],
				min : 0,
				max : '.(ceil($ride->max_speed / 10) * 10).'
            },
            tooltip: {
                formatter: function() {
                        return \'<b>\'+ this.series.name +\'</b><br/>\'+
                        this.x +\': \'+ this.y;
                }
            },
            legend: {
                layout: \'vertical\',
                align: \'right\',
                verticalAlign: \'top\',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: '.json_encode(array_values($series)).',
			plotOptions: {
            series: {
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        });
    });
    
});
		</script>
		<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
		<input type="button" id="paste_gpx" value="Paste a GPS file" />';
			
			$this->showFooter();
		}
	}
	
	public function showHeader() {
		echo '<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>CycleGraph</title>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
		<link rel="stylesheet" href="css/jquery.ui.all.css" TYPE="text/css" media="all">
		<script src="js/highcharts.js"></script>
		<script src="js/gray.js"></script>
	</head>
	<body>';
	}
	
	private function showFooter() {
		echo '</body>
</html>';
	}
}