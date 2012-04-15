<?php

namespace CycleGraph\RideLog;

interface Adaptor {
	/**
	 * Parse a ride log file
	 * @param $filename String Path to the file to parse
	 * @return boolean Could the file be parsed ?
	 */
	public function ParseFile($filename);
	
	/**
	 * Get the parsing error message
	 * @return string Error message
	 */
	public function GetParseError();
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Ride. Do not add points to the ride, the parser will take care of it.
	 * @return \CycleGraph\ORM\Entiry\Ride Ride entity filled with data from the log file
	 */
	public function GetRideEntity();
	
	/**
	 * Return a filled \CycleGraph\ORM\Entity\Point. Do not add the point to the ride entity, the parser will take care of it.
	 * Return false/NULL if there is no more points to be parsed.
	 * @return \CycleGraph\ORM\Entiry\Point Point entity filled with data from the log file.
	 */
	public function GetPointEntity();
	
	/**
	 * Tell the adaptor the parsing is finished and can close.
	 */
	public function CloseFile();
}