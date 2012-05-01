<?php

namespace CycleGraph\RideLog\Adaptor;

use \CycleGraph\RideLog;

class CyclemeterCSV implements RideLog\Adaptor {
	protected $_errorMessage = '';
	protected $_frenchHeader = array('Temps','Temps de course','Temps de course (secs)','Temps arrêté','Temps arrêté (secs)','Latitude','Longitude','Élévation (mètres)','Distance (km)','Vitesse (km/h)','Allure','Allure (secs)','Vitesse moyenne (km/h)','Allure moyenne','Allure moyenne (secs)','Montée (mètres)','Descente (mètres)','Calories','Rythme cardiaque (bpm)','Rythme cardiaque moyen (bpm)','Cadence (rpm)','Cadence moyenne(rpm)');
	
	protected $_fp;
	
	private $_lang;
	
	/**
	 * @var \CycleGraph\ORM\Entity\Route
	 */
	private $_route;
	
	/**
	 * @var \CycleGraph\ORM\Entity\Ride
	 */
	private $_ride;
	private $_points = array();
	private $_current_point = 0;
	
	const F_TIME = 0;
	const F_RIDE_TIME = 1;
	const F_RIDE_TIME_SEC = 2;
	const F_TIME_STOPPED = 3;
	const F_TIME_STOPPED_SEC = 4;
	const F_LATITUDE = 5;
	const F_LONGITUDE = 6;
	const F_ELEVATION = 7;
	const F_DISTANCE = 8;
	const F_SPEED = 9;
	const F_PACE = 10;
	const F_PACE_SEC = 11;
	const F_AVG_SPEED = 12;
	const F_AVG_PACE = 13;
	const F_AVG_PACE_SEC = 14;
	const F_ASCENT = 15;
	const F_DESCENT = 16;
	const F_CALORIES = 17;
	const F_HR = 18;
	const F_AVG_HR = 19;
	const F_CADENCE = 20;
	const F_AVG_CADENCE = 21;
	
	/**
	 * Look like \ReflectionClass->newInstance rquires the class to have a constructor...
	 */
	public function __constructor() { }
	
	public function setRoute(\CycleGraph\ORM\Entity\Route $route) {
		$this->_route = $route;
	}
	
	/**
	 * Parse a ride log file
	 * @param $filename String Path to the file to parse
	 * @return boolean Could the file be parsed ?
	 */
	public function parseFile($filename) {
		$fp = fopen($filename, 'r');
		
		if(!$fp) {
			$this->_errorMessage = 'File could not be read';
			return false;
		}
		
		$header = fgetcsv($fp, 0, ';');
		
		if(!$header) {
			fclose($fp);
			$this->_errorMessage = 'Header was emtpy';
			return false;
		}
		
		if(count($header) != 22) {
			fclose($fp);
			$this->_errorMessage = 'Header should contain 22 columns';
			return false;
		}
		
		$french_match = true;
		foreach($header as $index => $name) {
			if($this->_frenchHeader[$index] != $name) {
				$french_match = false;
				break;
			}
		}
		
		if($french_match) {
			$this->_lang = 'fr';
		}
		
		$header_match = $french_match; // || $english_match; etc.
		
		if(!$header_match) {
			fclose($fp);
			$this->_errorMessage = 'Could not match the header to any known language (only french is known so far)';
			return false;
		}
		
		$this->_ride = new \CycleGraph\ORM\Entity\Ride();
		$this->_ride->route = $this->_route;
		$this->_route->rides[] = $this->_ride;
		
		$this->_ride->description = '';

		$this->_ride->maxSpeed = 0;
		$this->_ride->maxCadence = 0;
		$this->_ride->maxHeartRate = 0;

		if($line = fgetcsv($fp, 0, ';')) {
			$this->_ride->date = substr($line[self::F_TIME], 0, 10);
			$this->_ride->startTime = substr($line[self::F_TIME], 10);

			do {
				$this->_points[] = $this->parseLine($this->formatLine($line));
			} while($line = fgetcsv($fp, 0, ';'));
			
			fclose($fp);
		}
		else {
			fclose($fp);
			$this->_errorMessage = 'No ride point could be generated';
			return false;
		}

		if(count($this->_points) == 0) {
			fclose($fp);
			$this->_errorMessage = 'No ride point could be generated';
			return false;
		}
		else {
			$lastPoint = $this->_points[count($this->_points) - 1];

			$this->_ride->endTime = substr($lastPoint->realTime, 10);
			$this->_ride->duration = $lastPoint->duration;
			$this->_ride->stoppedTime = $lastPoint->stoppedTime;
			
			$this->_ride->avgSpeed = $lastPoint->avgSpeed;
			$this->_ride->avgCadence = $lastPoint->avgCadance;
			$this->_ride->avgHeartRate = $lastPoint->avgHeartRate;
			$this->_ride->calories = $lastPoint->calories;
			$this->_ride->distance = $lastPoint->distance;
			$this->_ride->ascent = $lastPoint->ascent;
			$this->_ride->descent = $lastPoint->descent;

			return true;
		}
	}
	
	/**
	 * Parse a line and fills a Point entity
	 * @param Array $line Parse line
	 * @return \CycleGraph\ORM\Entity\Point Point entity from parsed line
	 */
	private function parseLine($line) { 		
		$this->_ride->maxSpeed = max($this->_ride->maxSpeed, $line[self::F_SPEED]);
		$this->_ride->maxCadence = max($this->_ride->maxCadence, $line[self::F_CADENCE]);
		$this->_ride->maxHeartRate = max($this->_ride->maxHeartRate, $line[self::F_HR]);
		
		$point = new \CycleGraph\ORM\Entity\Point();
		
		$point->realTime = $line[self::F_TIME];
		$point->rideTime = $line[self::F_RIDE_TIME];
		$point->duration = $line[self::F_RIDE_TIME_SEC];
		$point->stoppedTime = $line[self::F_TIME_STOPPED_SEC];
		
		$point->latitude = $line[self::F_LATITUDE];
		$point->longitude = $line[self::F_LONGITUDE];
		$point->distance = $line[self::F_DISTANCE];
		$point->elevation = $line[self::F_ELEVATION];
		$point->ascent = $line[self::F_ASCENT];
		$point->descent = $line[self::F_DESCENT];
		
		$point->speed = $line[self::F_SPEED];
		$point->avgSpeed = $line[self::F_AVG_SPEED];
		
		$point->cadence = $line[self::F_CADENCE];
		$point->avgCadance = $line[self::F_AVG_CADENCE];
		
		$point->heartRate = $line[self::F_HR];
		$point->avgHeartRate = $line[self::F_AVG_HR];
		$point->calories = $line[self::F_CALORIES];
		
		$point->rawData = $this->getLineRawData($line);
		
		$point->ride = $this->_ride;
		$this->_ride->points[] = $point;
		

		return $point;
	}
	
	/**
	 * Get the parsing error message
	 * @return string Error message
	 */
	public function getParseError() {
		return $this->_errorMessage;
	}
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Ride. Do not add points to the ride, the parser will take care of it.
	 * @return \CycleGraph\ORM\Entiry\Ride Ride entity filled with data from the log file
	 */
	public function getRideEntity() {
		return $this->_ride;
	}
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Point. Do not add the point to the ride entity, the parser will take care of it.
	 * Return false/NULL if there is no more points to be parsed.
	 * @return \CycleGraph\ORM\Entiry\Point Point entity filled with data from the log file.
	 */
	public function getPointEntity() {
		if(isset($this->_points[$this->_current_point])) {
			return $this->_points[$this->_current_point++];
		}
		else {
			return false;
		}
	}
	
	private function getLineRawData($line) {
		$reflection = new \ReflectionClass(get_class());
		$constants = $reflection->getConstants();
		
		$raw = array();
		foreach($constants as $constant => $value) {
			$raw[$constant] = $line[$value];
		}
		
		return json_encode($raw);
	}
	
	private function formatLine($line) {
		if($this->_lang == 'fr') {
			foreach($line as $index => $value) {
				if(strpos($value, ',') >= 0)
					$line[$index] = str_replace(',', '.', $value);
			}
			
			return $line;
		}
		else {
			return $line;
		}
	}
}