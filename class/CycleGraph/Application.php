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
			
			$ride = $em->find('\CycleGraph\ORM\Entity\Ride', (int)$_GET['ride']);
			
			$series = array(
				'cadence' => array('name' => 'Cadence', 'data' => array()),
				'hr' => array('name' => 'HR', 'data' => array()),
				'avg_hr' => array('name' => 'AVG HR', 'data' => array()),
				'relative_avg_hr' => array('name' => 'Relative AVG HR', 'data' => array()),
				'elevation' => array('name' => 'Elevation', 'data' => array())
			);
			
			$series2 = array(
				'speed' => array('name' => 'Vitesse', 'data' => array()),
				'max_speed' => array('name' => 'Vitesse max', 'data' => array()),
				'avg_speed' => array('name' => 'Vitesse moy', 'data' => array()),
				'relative_avg_speed' => array('name' => 'Vitesse avg relative', 'data' => array())
			);	
			
			$points_lon_lat = array();
			$prev_stopped_time = 0;
			foreach($ride->points as $index => $point) {
				if($point->stoppedTime != $prev_stopped_time) {
					$prev_stopped_time = $point->stoppedTime;
					
					$bands[] = array(
						'from' => $index-1,
						'to' => $index,
						'color' => '#555555'
					);
				}
				
				$points_lon_lat[$index] = array('lon' => (float)$point->longitude, 'lat' => (float)$point->latitude);

				$series['cadence']['data'][] = array($index, (int)$point->cadence);
				$series['hr']['data'][] = array($index, (int)$point->heartRate);
				$series['avg_hr']['data'][] = array($index, (int)$ride->avgHeartRate);
				$series['relative_avg_hr']['data'][] = array($index, (int)$point->avgHeartRate);
				$series['elevation']['data'][] = array($index, (int)$point->elevation);

				$series2['speed']['data'][] = array($index, (float)$point->speed);
				$series2['max_speed']['data'][] = array($index, (float)$ride->maxSpeed);
				$series2['avg_speed']['data'][] = array($index, (float)$ride->avgSpeed);
				$series2['relative_avg_dspeed']['data'][] = array($index, (float)$point->avgSpeed);
			}
			
			echo '<div id="ride_map" style="height: 640px; width: 980px; margin: 0 auto;"></div>
				<div id="container" style="width: 980px; height: 400px; margin: 0 auto"></div>
				<div id="container2" style="width: 980px; height: 400px; margin: 0 auto"></div>
				<script>
			$(function() {
				 var chart;
				 var chart2;
				 var map;
				 
				 var marker;
				 
				 var points_lon_lat = '.json_encode($points_lon_lat).';

				$(document).ready(function() {
					var myOptions = {
						zoom: 12,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					map = new google.maps.Map(document.getElementById(\'ride_map\'),myOptions); 
					if(navigator.geolocation) {
					  navigator.geolocation.getCurrentPosition(function(position) {
						var pos = new google.maps.LatLng(position.coords.latitude,
														 position.coords.longitude);

						console.debug(position.coords.latitude);
						console.debug(position.coords.longitude);

						map.setCenter(pos);
						
						var marker_options = {
							clickable:false,
							draggable:false,
							map:map,
							position:pos
						};

						marker = new google.maps.Marker(marker_options);
					  });
					}
				
					chart = new Highcharts.Chart({
						chart: {
							renderTo: \'container\',
							type: \'spline\',
							marginRight: 130,
							marginBottom: 25
						},
						tooltip: {
							formatter: function() {
							    var s = \'<b>\'+ this.x +\'</b>\';

								$.each(this.points, function(i, point) {
									s += \'<br/>\'+ point.series.name +\': \'+ point.y +\'m\';
								});
								
								console.debug(points_lon_lat[this.x]);
								
								var p = new google.maps.LatLng(points_lon_lat[this.x].lat, points_lon_lat[this.x].lon);
								console.debug(p);
								marker.setPosition(p);

								
									map.panTo(p);	
									

								return s;
							},
							shared: true
						},
						title: {
							text: \'' . $ride->name . '\',
							x: -20 //center
						},
						xAxis: {
							type:\'linear\',
							plotBands: ' . json_encode($bands) . ',
						},
						yAxis: {
							title: {
								text: \'Speed/HR\'
							},
							plotLines: [{
								value: 0,
								width: 1,
								color: \'#808080\'
							}],
							min : 0
						},
						legend: {
							layout: \'vertical\',
							align: \'right\',
							verticalAlign: \'top\',
							x: -10,
							y: 100,
							borderWidth: 0
						},
						series: ' . json_encode(array_values($series)) . ',
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
						}
					});

					chart2 = new Highcharts.Chart({
						chart: {
							renderTo: \'container2\',
							type: \'spline\',
							marginRight: 130,
							marginBottom: 25
						},
						title: {
							text: \'' . $ride->name . '\',
							x: -20 //center
						},
						xAxis: {
							type:\'linear\',
							plotBands: ' . json_encode($bands) . ',
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
							max : ' . (ceil($ride->maxSpeed / 10) * 10) . '
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
						series: ' . json_encode(array_values($series2)) . ',
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
		';
			
		
			
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
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
	</head>
	<body>';
	}
	
	private function showFooter() {
		echo '</body>
</html>';
	}
}