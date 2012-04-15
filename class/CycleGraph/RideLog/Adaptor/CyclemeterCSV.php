<?php

namespace CycleGraph\RideLog\Adaptor;

use \CycleGraph\RideLog;

class CyclemeterCSV implements RideLog\Adaptor {
	protected $_error_message = '';
	protected $_french_header = array('Temps','Temps de course','Temps de course (secs)','Temps arrêté','Temps arrêté (secs)','Latitude','Longitude','Élévation (mètres)','Distance (km)','Vitesse (km/h)','Allure','Allure (secs)','Vitesse moyenne (km/h)','Allure moyenne','Allure moyenne (secs)','Montée (mètres)','Descente (mètres)','Calories','Rythme cardiaque (bpm)','Rythme cardiaque moyen (bpm)','Cadence (rpm)','Cadence moyenne(rpm)');
	
	protected $_fp;
	
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
		
		$header_match = $french_match; // || $english_match; etc.
		
		if(!$header_match) {
			fclose($fp);
			$this->_error_message = 'Could not match the header to any known language (only french is known so far)';
			return false;
		}
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
		
	}
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Point. Do not add the point to the ride entity, the parser will take care of it.
	 * Return false/NULL if there is no more points to be parsed.
	 * @return \CycleGraph\ORM\Entiry\Point Point entity filled with data from the log file.
	 */
	public function GetPointEntity() {
		
	}
	
	/**
	 * Tell the adaptor the parsing is finished and can close.
	 */
	public function CloseFile() {
		if($fp) {
			fclose($fp);
		}
	}
}