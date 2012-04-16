<?php

namespace CycleGraph\RideLog\Adaptor;

use \CycleGraph\RideLog;

class CyclemeterCSV implements RideLog\Adaptor {
	protected $_error_message = '';
	protected $_french_header = array('Temps','Temps de course','Temps de course (secs)','Temps arrêté','Temps arrêté (secs)','Latitude','Longitude','Élévation (mètres)','Distance (km)','Vitesse (km/h)','Allure','Allure (secs)','Vitesse moyenne (km/h)','Allure moyenne','Allure moyenne (secs)','Montée (mètres)','Descente (mètres)','Calories','Rythme cardiaque (bpm)','Rythme cardiaque moyen (bpm)','Cadence (rpm)','Cadence moyenne(rpm)');
	
	protected $_fp;
	
	private $_lang;
	
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
	
	/**
	 * Parse a ride log file
	 * @param $filename String Path to the file to parse
	 * @return boolean Could the file be parsed ?
	 */
	public function ParseFile($filename) {
		$fp = fopen($filename, 'r');
		
		if(!$fp) {
			$this->_error_message = 'File could not be read';
			return false;
		}
		
		$header = fgetcsv($fp, 0, ';');
		
		if(!$header) {
			fclose($fp);
			$this->_error_message = 'Header was emtpy';
			return false;
		}
		
		if(count($header) != 22) {
			fclose($fp);
			$this->_error_message = 'Header should contain 22 columns';
			return false;
		}
		
		$french_match = true;
		foreach($header as $index => $name) {
			if($this->_french_header[$index] != $name) {
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
			$this->_error_message = 'Could not match the header to any known language (only french is known so far)';
			return false;
		}
		
		$this->_ride = new \CycleGraph\ORM\Entity\Ride();

		$pathinfo = pathinfo($filename);
		$this->_ride->name = $pathinfo['filename'];
		$this->_ride->description = '';

		$this->_ride->max_speed = 0;
		$this->_ride->max_cadence = 0;
		$this->_ride->max_hr = 0;

		if($line = fgetcsv($fp, 0, ';')) {
			$this->_ride->date = substr($line[self::F_TIME], 0, 10);
			$this->_ride->start_time = substr($line[self::F_TIME], 10);

			do {
				$this->_points[] = $this->ParseLine($this->FormatLine($line));
			} while($line = fgetcsv($fp, 0, ';'));
			
			fclose($fp);
		}
		else {
			fclose($fp);
			$this->_error_message = 'No ride point could be generated';
			return false;
		}

		if(count($this->_points) == 0) {
			fclose($fp);
			$this->_error_message = 'No ride point could be generated';
			return false;
		}
		else {
			$last_point = $this->_points[count($this->_points) - 1];

			$this->_ride->end_time = substr($last_point->real_time, 10);

			$this->_ride->avg_speed = $last_point->avg_speed;
			$this->_ride->avg_cadence = $last_point->avg_cadance;
			$this->_ride->avg_hr = $last_point->avg_hr;
			$this->_ride->distance = $last_point->distance;
			$this->_ride->ascent = $last_point->ascent;

			return true;
		}
	}
	
	/**
	 * Parse a line and fills a Point entity
	 * @param Array $line Parse line
	 * @return \CycleGraph\ORM\Entity\Point Point entity from parsed line
	 */
	private function ParseLine($line) { 		
		$this->_ride->max_speed = max($this->_ride->max_speed, $line[self::F_SPEED]);
		$this->_ride->max_cadence = max($this->_ride->max_cadence, $line[self::F_CADENCE]);
		$this->_ride->max_hr = max($this->_ride->max_hr, $line[self::F_HR]);
		
		$point = new \CycleGraph\ORM\Entity\Point();
		
		$point->real_time = $line[self::F_TIME];
		$point->ride_time = $line[self::F_RIDE_TIME];
		
		$point->latitude = $line[self::F_LATITUDE];
		$point->longitude = $line[self::F_LONGITUDE];
		$point->distance = $line[self::F_DISTANCE];
		$point->elevation = $line[self::F_ELEVATION];
		$point->ascent = $line[self::F_ASCENT];
		$point->descent = $line[self::F_DESCENT];
		
		$point->speed = $line[self::F_SPEED];
		$point->avg_speed = $line[self::F_AVG_SPEED];
		
		$point->cadence = $line[self::F_CADENCE];
		$point->avg_cadance = $line[self::F_AVG_CADENCE];
		
		$point->hr = $line[self::F_HR];
		$point->avg_hr = $line[self::F_AVG_HR];
		$point->calories = $line[self::F_CALORIES];
		
		$point->raw_data = $this->GetLineRawData($line);
		
		$point->ride = $this->_ride;
		$this->_ride->points[] = $point;
		

		return $point;
	}
	
	/**
	 * Get the parsing error message
	 * @return string Error message
	 */
	public function GetParseError() {
		return $this->_error_message;
	}
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Ride. Do not add points to the ride, the parser will take care of it.
	 * @return \CycleGraph\ORM\Entiry\Ride Ride entity filled with data from the log file
	 */
	public function GetRideEntity() {
		return $this->_ride;
	}
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Point. Do not add the point to the ride entity, the parser will take care of it.
	 * Return false/NULL if there is no more points to be parsed.
	 * @return \CycleGraph\ORM\Entiry\Point Point entity filled with data from the log file.
	 */
	public function GetPointEntity() {
		if(isset($this->_points[$this->_current_point])) {
			return $this->_points[$this->_current_point++];
		}
		else {
			return false;
		}
	}
	
	private function GetLineRawData($line) {
		$reflection = new \ReflectionClass(get_class());
		$constants = $reflection->getConstants();
		
		$raw = array();
		foreach($constants as $constant => $value) {
			$raw[$constant] = $line[$value];
		}
		
		return json_encode($raw);
	}
	
	private function FormatLine($line) {
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