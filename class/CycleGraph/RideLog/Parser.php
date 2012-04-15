<?php

namespace ClycleGraph\RideLog;

class Parser {
	private $_adaptor;
	private $_em;
	
	public function __construct(\Doctrine\ORM\EntityManager $em, $adaptor='\CycleGraph\RideLog\Adaptor\CyclemeterCSV') {
		if(is_string($adaptor)) {
			$reflection = new ReflectionClass($adaptor);
		
			if($reflection->implementsInterface('\CycleGraph\RideLog\ParserAdaptor')) {
				$this->_adaptor = $reflection->newInstance($adaptor);
			}
			else {
				throw new \Exception($adaptor.' does not implement \CycleGraph\RideLog\ParserAdaptor');
			}
		}
		else if($adaptor instanceof \CycleGraph\RideLog\Adaptor) {
			$this->_adaptor = $adaptor;
		}
		else {
			throw new \Exception('The given adaptor must implement \CycleGraph\RideLog\ParserAdaptor');
		}
	}
	
	public function ParseFile($filename) {
		if(!file_exists($filename)) {
			throw new \Exception('File does not exists : '.$filename);
		}
		
		$this->_adaptor->ParseFile($filename);
		
		$ride = $this->_adaptor->GetRideEntity();
		while($point = $this->_adaptor->GetPointEntity()) {
			$ride->AddPoint($point);
		}
		$this->_adaptor->CloseFile();
		
		return $ride;
	}
}